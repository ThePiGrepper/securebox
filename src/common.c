#include "common.h"
#include "event_manager.h"
#include "settings.h"

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
  //gprsDrv_Setup();
  //gpsDrv_Setup();
  wifiDrv_Setup();
  common_Setup();
  LOCK_OFF;
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
  wifiSetStatus(wifi_setup);
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
