#include "common.h"
#include "event_manager.h"
#include "settings.h"
#include <stdio.h>
#include <string.h>

char outstr[100];
void gprsSendCoord(char *str){
   const char s[] = ",";
   char *token;
   char time[15];
   char ilat[15];
   char *dlat;
   char ilon[15];
   char *dlon;
   char slat[2]={0,0};
   char slon[2]={0,0};
   int val1;
   int val2;
   unsigned char i=0;
   char *ptr;
   /* get the first token */
   token = strtok(str, s);
   /* walk through other tokens */
   while( token != NULL )
   {
      switch(i)
      {
        case 0: //time
               strcpy(time,token);
               time[6]=0;
               break;
        case 2:
               strcpy(ilat,token);
               ptr=strchr(ilat,'.');
               *ptr=*(ptr-1);
               *(ptr-1)=*(ptr-2);
               *(ptr-2)=0;
               dlat=ptr-1;
               break;
        case 3:
               if('S'==token[0])
                 slat[0]='-';
               break;
        case 4:
               strcpy(ilon,token);
               ptr=strchr(ilon,'.');
               *ptr=*(ptr-1);
               *(ptr-1)=*(ptr-2);
               *(ptr-2)=0;
               dlon=ptr-1;
               break;
        case 5:
               if('W'==token[0])
                 slon[0]='-';
               break;
      }
      i++;
     token = strtok(NULL, s);
  }
  val1=strtol(dlat,NULL,10)/60;
  val2=strtol(dlon,NULL,10)/60;
  sprintf(outstr,"lat=%s%s.%d&long=%s%s.%d", slat,ilat,val1,slon,ilon,val2);
  //sprintf(outstr,"time:%s, lon:%s%s.%d,lat:%s%s.%d\n", time,slat,ilat,val1,slon,ilon,val2);
  //gprsDrvOUT_puts(outstr,0);
  //return(0);
}

static void common_Setup(void){
  GPIO_InitTypeDef GPIO_InitStructure;
  /* GPIOA Periph clock enable */
  RCC_AHB1PeriphClockCmd(RCC_AHB1Periph_GPIOA, ENABLE);
  /* Configure PA5 in output pushpull mode */
  GPIO_InitStructure.GPIO_Pin = GPIO_Pin_5;
  GPIO_InitStructure.GPIO_Speed =GPIO_Medium_Speed;
  GPIO_InitStructure.GPIO_Mode = GPIO_Mode_OUT;
  GPIO_InitStructure.GPIO_OType = GPIO_OType_PP;
  GPIO_InitStructure.GPIO_PuPd = GPIO_PuPd_NOPULL;
  GPIO_Init(GPIOA, &GPIO_InitStructure);

	/* Systick Configuration */
  if (SysTick_Config(SystemCoreClock / 5000))
  {
    while(1);
  }

}

void HW_setup(void){
#ifdef  VECT_TAB_RAM
  /* Set the Vector Table base location at 0x20000000 */
  NVIC_SetVectorTable(NVIC_VectTab_RAM, 0x0);
#else  /* VECT_TAB_FLASH  */
  /* Set the Vector Table base location at 0x08000000 */
  NVIC_SetVectorTable(NVIC_VectTab_FLASH, 0x0);
#endif
  /* Configure one bit for preemption priority */
  NVIC_PriorityGroupConfig(NVIC_PriorityGroup_1);
  common_Setup();
  gprsDrv_Setup();
  gpsDrv_Setup();
  wifiDrv_Setup();
  rpiDrv_Setup();
  LOCK_ON;
  //for(uint32_t i=0;i<100000;i++);
  //int i;
  //for(i=0;i<1000000;i++);
  Delay(1000);
  LOCK_OFF;
  //for(i=0;i<1000000;i++);
  Delay(1000);
  LOCK_ON;
}

static volatile uint32_t TimingDelay;
/**
  * @brief  Inserts a delay time.
  * @param  nTime: specifies the delay time length, in milliseconds.
  * @retval None
  */
void Delay(volatile uint32_t nTime){
  TimingDelay = nTime;
  while(TimingDelay != 0);
}

/**
  * @brief  Decrements the TimingDelay variable.
  * @param  None
  * @retval None
  */
void TimingDelay_Decrement(void){
  if (TimingDelay != 0)
  {
    TimingDelay--;
  }
}

volatile msgPipe gprsPipe={0,0,0};
void waitforit(int32_t timeout){
  gprsPipe.enabled=1;
  TimingDelay=timeout;
  while(TimingDelay > 0)
  {
    if(gprsPipe.sent == 1)
    {
      TimingDelay=timeout;
      gprsPipe.sent = 0;
    }
  }
  gprsPipe.sent = 0;
  gprsPipe.enabled=0;
}

typedef enum { status_opened,status_locked } lockStatus;

void proto_main(void){
  //stages: status_opened, status_locked.
  //status_opened->status_locked: needs: setup=1.
  //  LOCK_ON
  //  starts reading gps
  //  starts receiving syncro data
  //  start sending gprs each T seconds.
  //status_locked->status_opened:
  //  gps_match()=1 then auth_on //needs global var for wifi_state: {disabled,setup,auth}
  //  gps_match()=0 when disable_wifi()
  event_type currEvent;
  lockStatus currStatus = status_opened;
  lockStatus nextStatus = status_opened;
  //set to status_opened
  HW_setup();
  //wifiSetStatus(wifi_setup);
  //gprsDrvOUT_puts("this is a test",0);
  uint8_t *streamPtr;
  //gprsDrvOUT_puts("testing..",0);
  while(1)
  {
    char *ptr;
    int32_t event_id = EM_getEvent(&currEvent);
    if(event_id != -1)
    {
      switch(currEvent)
      {
        case wifi_e:
          wifiDrvIN_read(&streamPtr);
          gprsDrvOUT_write('+');
          gprsDrvOUT_puts(streamPtr,'\n');
          gprsDrvOUT_write('\n');
          break;
        case gprs_e:
          gprsDrvIN_read(&streamPtr);
          //LOCK_OFF;
          //wifiDrvOUT_puts(streamPtr,'\n');
          //wifiDrvOUT_write('\n');
          break;
        case gps_e:
          gpsDrvIN_read(&streamPtr);
          if(strchr(streamPtr,'V') == NULL)
          {
            *strchr(streamPtr,'\r')=0;
            gprsSendCoord(streamPtr);
            gprsDrv_SendData(outstr);
            //wifiDrvOUT_puts(outstr,0);
            //wifiDrvOUT_write('\n');
            //gprsDrvOUT_puts(streamPtr,'\n');
            //gprsDrvOUT_write('\n');
          }
          break;
        case rpi_e:
          rpiDrvIN_read(&streamPtr);
          break;
        default:
          wifiDrvOUT_write('+');
          wifiDrvOUT_puts("oops\n",0);
      }
    }
  }
  //status_opened
  while(1)
  {
    currStatus = nextStatus;
    int32_t event_id = EM_getEvent(&currEvent);
    switch(currStatus)
    {
      case status_opened:
        //reset status
        if(event_id != -1)
        {
          switch(currEvent)
          {
            case wifi_e:
              //handle setup info
              //if setup info is ok, go to status_locked
              //do lock init setup HERE
              wifiSetStatus(wifi_disabled);
              nextStatus = status_locked;
              break;
            case gps_e:
              //discard data
              break;
          }
        }
        break;
      case status_locked:
        if(event_id != -1)
        {
          switch(currEvent)
          {
            case wifi_e:
              //discard data and disable
              break;
            case gps_e:
              //receive string and extract time and coordinates
              //save a value set each 10 samples and send to gprs
              //if they match to destination coords for 10 samples it enables wifi_auth.
              //if at least 10 samples dont match, it goes back to wifi_disabled.

              break;
          }
        }
        break;
      default:
        nextStatus = status_locked;
    }
	}
}
