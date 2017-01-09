#include "gprsDrv.h"
#include "common.h"
#include "settings.h"
#include "simple_queue.h"
#include "event_manager.h"

static uint8_t dinBuf[GPRSDRV_BUFIN_SZ*2];
static ringBuf_t dinBufCtrl = {dinBuf,0,0,GPRSDRV_BUFIN_SZ};

static gprsDrvIN_frame doutPool[GPRSDRV_BUFOUT_SZ]={0};
static uint32_t doutBuf[GPRSDRV_BUFOUT_SZ]={0};
static uint32_t poolCount=0;
static ringBuf32_t doutBufCtrl = {doutBuf,0,GPRSDRV_BUFOUT_SZ-1,GPRSDRV_BUFOUT_SZ};

static uint32_t currLen=0;
static int currStr=0;

/*Function Implementations */

static int32_t getINBufFreeSpace(void)
{
  return ((dinBufCtrl.tail-dinBufCtrl.head) > 0)?
    dinBufCtrl.tail-dinBufCtrl.head-1:GPRSDRV_BUFIN_SZ+(dinBufCtrl.tail-dinBufCtrl.head)-1;
}

void gprsParse(uint8_t data){
  gprsDrvIN_write(data);
}

int32_t gprsDrvIN_read(uint8_t **ptr){
  uint32_t count;
  int str = doutPool[doutBuf[doutBufCtrl.tail]].str;
  uint32_t len = doutPool[doutBuf[doutBufCtrl.tail]].len;
  if(ringBufSPop32(&doutBufCtrl,&count)) return -1; //OUT buffer empty
  //release dinBuf
  dinBufCtrl.tail = ((str + (int)len + 1 - GPRSDRV_BUFIN_SZ) >= 0)?
    str + len + 1 - GPRSDRV_BUFIN_SZ : str + len + 1;
  *ptr = &dinBuf[doutPool[count].str];
  return doutPool[count].len;
}

int32_t gprsDrvIN_write(uint8_t data){
  if(ringBufPush(&dinBufCtrl,data)) return -1; //IN buffer full
  //writes out of boundary to enable regular string manipulation
  if((currStr + currLen) >= GPRSDRV_BUFIN_SZ)
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
    if(poolCount >= GPRSDRV_BUFOUT_SZ)
      poolCount = 0;
    currStr = dinBufCtrl.head;
    currLen = 0;
    if(EM_setEvent(gprs_e) < 0) return -3; //event queue full
  }
  else
  {
    currLen++;
  }
  //return dinBufCtrl.head;
  return getINBufFreeSpace();
}

#define TIMEOUT 300
#include <stdio.h>
void gprsDrv_Init(void){
  waitforit(TIMEOUT);
  //gprsDrvOUT_puts("AT+SAPBR = 3,1,\"CONTYPE\",\"GPRS\"\r\n",0);
  gprsDrvOUT_puts("AT+SAPBR = 1,1\r\n",0);
  waitforit(TIMEOUT);
  gprsDrvOUT_puts("AT+HTTPINIT\r\n",0);
  waitforit(TIMEOUT);
  gprsDrvOUT_puts("ATE0\r\n",0);
}

void gprsDrv_SendData(const char *pkg){
  char str[200];
  //lat=-12.52&long=-76.589
  waitforit(TIMEOUT);
  gprsDrvOUT_puts("AT+SAPBR = 3,1,\"APN\",\"movistar\"\r\n",0);
  wifiDrvOUT_puts("AT+SAPBR = 3,1,\"APN\",\"movistar\"\r\n",0);
  waitforit(TIMEOUT);
  sprintf(str,"AT+HTTPPARA = \"URL\",\"http://190.216.184.54/guarda_coordenadas1.php?%s\"\r\n",pkg);
  gprsDrvOUT_puts(str,0);
  waitforit(TIMEOUT);
  gprsDrvOUT_puts("AT+HTTPACTION=0\r\n",0);
  waitforit(TIMEOUT);
}

void gprsDrv_Setup(void){
  /* buffers startup values */
  doutBuf[GPRSDRV_BUFOUT_SZ-1] = GPRSDRV_BUFOUT_SZ-1;
  doutPool[GPRSDRV_BUFOUT_SZ-1].str = GPRSDRV_BUFIN_SZ-1;
  doutPool[GPRSDRV_BUFOUT_SZ-1].len = 0;
  /* Hardware setup */
  GPIO_InitTypeDef GPIO_InitStructure;
  USART_InitTypeDef USART_InitStructure;
  NVIC_InitTypeDef NVIC_InitStructure;
  /* GPRS_PORT Periph clock enable */
  RCC_AHB1PeriphClockCmd(GPRS_GPIO_CLK, ENABLE);
  GPRS_MODULE_CLK_SETUP(GPRS_MODULE_CLK, ENABLE);
  /* GPRS Port Configuration */
  GPIO_InitStructure.GPIO_Pin = GPRS_TX_PIN | GPRS_RX_PIN;
  GPIO_InitStructure.GPIO_Speed =GPIO_Medium_Speed;
  GPIO_InitStructure.GPIO_Mode = GPIO_Mode_AF;
  GPIO_InitStructure.GPIO_OType = GPIO_OType_PP;
  GPIO_InitStructure.GPIO_PuPd = GPIO_PuPd_NOPULL;
  GPIO_Init(GPRS_PORT, &GPIO_InitStructure);

  GPIO_InitStructure.GPIO_Pin = GPRS_START_PIN;
  GPIO_InitStructure.GPIO_Mode = GPIO_Mode_OUT;
  GPIO_Init(GPRS_PORT2, &GPIO_InitStructure);

  GPIO_PinAFConfig(GPRS_PORT,GPRS_TX_PINSOURCE,GPRS_MODULE_AF);
  GPIO_PinAFConfig(GPRS_PORT,GPRS_RX_PINSOURCE,GPRS_MODULE_AF);

  USART_StructInit(&USART_InitStructure);
  USART_InitStructure.USART_BaudRate = 115200;
  USART_Init(GPRS_MODULE,&USART_InitStructure);

  /* Configure GPRS interrupt */
  NVIC_InitStructure.NVIC_IRQChannel = GPRS_IRQ;
  NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority = 0;
  NVIC_InitStructure.NVIC_IRQChannelSubPriority = 1;
  NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE;
  NVIC_Init(&NVIC_InitStructure);
  USART_ITConfig(GPRS_MODULE, GPRS_RX_IT, ENABLE);
  //USART_ITConfig(GPRS_MODULE, GPRS_TX_IT, ENABLE);
  USART_Cmd(GPRS_MODULE, ENABLE);
  gprsSetStatus(ENABLED);
  Delay(10000);
  gprsDrv_Init();
}

static gprsStatus currStatus = DISABLED;
int32_t gprsSetStatus(gprsStatus status){
  if(status != currStatus)
  {
    //execute ON/OFF routine
    Delay(2000);
    GPIO_SetBits(GPRS_PORT2, GPRS_START_PIN);
    Delay(2000);
    GPIO_ResetBits(GPRS_PORT2, GPRS_START_PIN);
    currStatus = status;
  }
  return status;
}

gprsStatus gprsGetStatus(void){
  return currStatus;
}

int32_t gprsDrvOUT_write(uint8_t data){
  while(SET != USART_GetFlagStatus(GPRS_MODULE, USART_FLAG_TC));
  USART_SendData(GPRS_MODULE,data);
  return 0;
}

//returns number of bytes sent
int32_t gprsDrvOUT_puts(char *str,char ch){
  uint32_t i=0;
  while(*(str+i) != ch)
  {
    gprsDrvOUT_write(*(str+i));
    i++;
  }
  return i;
}
