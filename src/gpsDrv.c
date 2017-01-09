#include "gpsDrv.h"
#include "settings.h"
#include "simple_queue.h"
#include "event_manager.h"

static uint8_t dinBuf[GPSDRV_BUFIN_SZ*2];
static ringBuf_t dinBufCtrl = {dinBuf,0,0,GPSDRV_BUFIN_SZ};

static gpsDrvIN_frame doutPool[GPSDRV_BUFOUT_SZ]={0};
static uint32_t doutBuf[GPSDRV_BUFOUT_SZ]={0};
static uint32_t poolCount=0;
static ringBuf32_t doutBufCtrl = {doutBuf,0,GPSDRV_BUFOUT_SZ-1,GPSDRV_BUFOUT_SZ};

static uint32_t currLen=0;
static int currStr=0;

/*Function Implementations */

static int32_t getINBufFreeSpace(void)
{
  return ((dinBufCtrl.tail-dinBufCtrl.head) > 0)?
    dinBufCtrl.tail-dinBufCtrl.head-1:GPSDRV_BUFIN_SZ+(dinBufCtrl.tail-dinBufCtrl.head)-1;
}

static const uint8_t cmd[]="$GPRMC,";
static uint8_t cmdCounter = 0;
#define CMD_SIZE 7

//parse gps data stream.
//gets data from GPRMC data
//sends to buffer only
void gpsParse(uint8_t data){
  //match GPRMC
  if(cmdCounter<CMD_SIZE)
  {
    if(cmd[cmdCounter++] != data)
      cmdCounter=0;
  }
  else //eat the rest up to \r\n
  {
    gpsDrvIN_write(data);
    if(data == '\n')
    {
      cmdCounter=0;
    }
  }
}

int32_t gpsDrvIN_read(uint8_t **ptr){
  uint32_t count;
  int str = doutPool[doutBuf[doutBufCtrl.tail]].str;
  uint32_t len = doutPool[doutBuf[doutBufCtrl.tail]].len;
  if(ringBufSPop32(&doutBufCtrl,&count)) return -1; //OUT buffer empty
  //release dinBuf
  dinBufCtrl.tail = ((str + (int)len + 1 - GPSDRV_BUFIN_SZ) >= 0)?
    str + len + 1 - GPSDRV_BUFIN_SZ : str + len + 1;
  *ptr = &dinBuf[doutPool[count].str];
  return doutPool[count].len;
}

int32_t gpsDrvIN_write(uint8_t data){
  if(ringBufPush(&dinBufCtrl,data)) return -1; //IN buffer full
  //writes out of boundary to enable regular string manipulation
  if((currStr + currLen) >= GPSDRV_BUFIN_SZ)
    dinBuf[currStr + currLen] = data;
  if(data == '\n') //send frame to buffer
  {
    if(ringBufPush32(&doutBufCtrl,poolCount))
    {
      dinBufCtrl.head = currStr; //flush incomplete frame
      currLen = 0;
      return -2; //OUT buffer full
    }
    doutPool[poolCount].str = currStr;
    doutPool[poolCount++].len = currLen;
    if(poolCount >= GPSDRV_BUFOUT_SZ)
      poolCount = 0;
    currStr = dinBufCtrl.head;
    currLen = 0;
    if(EM_setEvent(gps_e) < 0) return -3; //event queue full
  }
  else
  {
    currLen++;
  }
  //return dinBufCtrl.head;
  return getINBufFreeSpace();
}

//returns number of bytes sent
int32_t gpsDrvOUT_puts(char *str,char ch){
  uint32_t i=0;
  while(*(str+i) != ch)
  {
    gpsDrvOUT_write(*(str+i));
    i++;
  }
  return i;
}

int32_t gpsDrvOUT_write(uint8_t data){
  while(SET != USART_GetFlagStatus(GPS_MODULE, USART_FLAG_TC));
  USART_SendData(GPS_MODULE,data);
  return 0;
}

void gpsDrv_Setup(void){
  /* buffers startup values */
  doutBuf[GPSDRV_BUFOUT_SZ-1] = GPSDRV_BUFOUT_SZ-1;
  doutPool[GPSDRV_BUFOUT_SZ-1].str = GPSDRV_BUFIN_SZ-1;
  doutPool[GPSDRV_BUFOUT_SZ-1].len = 0;
  /* Hardware setup */
  GPIO_InitTypeDef GPIO_InitStructure;
  USART_InitTypeDef USART_InitStructure;
  NVIC_InitTypeDef NVIC_InitStructure;
  /* GPS_PORT Periph clock enable */
  RCC_AHB1PeriphClockCmd(GPS_GPIO_CLK, ENABLE);
  GPS_MODULE_CLK_SETUP(GPS_MODULE_CLK, ENABLE);
  /* GPS Port Configuration */
  GPIO_InitStructure.GPIO_Pin = GPS_TX_PIN | GPS_RX_PIN;
  GPIO_InitStructure.GPIO_Speed =GPIO_Medium_Speed;
  GPIO_InitStructure.GPIO_Mode = GPIO_Mode_AF;
  GPIO_InitStructure.GPIO_OType = GPIO_OType_PP;
  GPIO_InitStructure.GPIO_PuPd = GPIO_PuPd_NOPULL;
  GPIO_Init(GPS_PORT, &GPIO_InitStructure);

  GPIO_PinAFConfig(GPS_PORT,GPS_TX_PINSOURCE,GPS_MODULE_AF);
  GPIO_PinAFConfig(GPS_PORT,GPS_RX_PINSOURCE,GPS_MODULE_AF);

  USART_StructInit(&USART_InitStructure);
  USART_InitStructure.USART_BaudRate = 9600;
  USART_Init(GPS_MODULE,&USART_InitStructure);

  /* Configure GPS interrupt */
  NVIC_InitStructure.NVIC_IRQChannel = GPS_IRQ;
  NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority = 0;
  NVIC_InitStructure.NVIC_IRQChannelSubPriority = 1;
  NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE;
  NVIC_Init(&NVIC_InitStructure);
  USART_ITConfig(GPS_MODULE, GPS_RX_IT, ENABLE);
  //USART_ITConfig(GPS_MODULE, GPS_TX_IT, ENABLE);
  USART_Cmd(GPS_MODULE, ENABLE);
}
