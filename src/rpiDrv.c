#include "rpiDrv.h"
#include "settings.h"
#include "simple_queue.h"
#include "event_manager.h"

static uint8_t dinBuf[RPIDRV_BUFIN_SZ*2];
static ringBuf_t dinBufCtrl = {dinBuf,0,0,RPIDRV_BUFIN_SZ};

static rpiDrvIN_frame doutPool[RPIDRV_BUFOUT_SZ]={0};
static uint32_t doutBuf[RPIDRV_BUFOUT_SZ]={0};
static uint32_t poolCount=0;
static ringBuf32_t doutBufCtrl = {doutBuf,0,RPIDRV_BUFOUT_SZ-1,RPIDRV_BUFOUT_SZ};

static uint32_t currLen=0;
static int currStr=0;

/*Function Implementations */

static int32_t getINBufFreeSpace(void)
{
  return ((dinBufCtrl.tail-dinBufCtrl.head) > 0)?
    dinBufCtrl.tail-dinBufCtrl.head-1:RPIDRV_BUFIN_SZ+(dinBufCtrl.tail-dinBufCtrl.head)-1;
}

//parse rpi data stream.
//gets data from GPRMC data
//sends to buffer only
static uint8_t parse_count = 0;
void rpiParse(uint8_t data){
  if(parse_count < 2)
  {
    if(data == '$')
      parse_count++;
    else
      parse_count=0;
  }
  else
  {
    rpiDrvIN_write(data);
    if(data == '\n')
      parse_count=0;
  }
}

int32_t rpiDrvIN_read(uint8_t **ptr){
  uint32_t count;
  int str = doutPool[doutBuf[doutBufCtrl.tail]].str;
  uint32_t len = doutPool[doutBuf[doutBufCtrl.tail]].len;
  if(ringBufSPop32(&doutBufCtrl,&count)) return -1; //OUT buffer empty
  //release dinBuf
  dinBufCtrl.tail = ((str + (int)len + 1 - RPIDRV_BUFIN_SZ) >= 0)?
    str + len + 1 - RPIDRV_BUFIN_SZ : str + len + 1;
  *ptr = &dinBuf[doutPool[count].str];
  return doutPool[count].len;
}

int32_t rpiDrvIN_write(uint8_t data){
  if(ringBufPush(&dinBufCtrl,data)) return -1; //IN buffer full
  //writes out of boundary to enable regular string manipulation
  if((currStr + currLen) >= RPIDRV_BUFIN_SZ)
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
    if(poolCount >= RPIDRV_BUFOUT_SZ)
      poolCount = 0;
    currStr = dinBufCtrl.head;
    currLen = 0;
    if(EM_setEvent(rpi_e) < 0) return -3; //event queue full
  }
  else
  {
    currLen++;
  }
  //return dinBufCtrl.head;
  return getINBufFreeSpace();
}

void rpiDrv_Setup(void){
  /* buffers startup values */
  doutBuf[RPIDRV_BUFOUT_SZ-1] = RPIDRV_BUFOUT_SZ-1;
  doutPool[RPIDRV_BUFOUT_SZ-1].str = RPIDRV_BUFIN_SZ-1;
  doutPool[RPIDRV_BUFOUT_SZ-1].len = 0;
  /* Hardware setup */
  GPIO_InitTypeDef GPIO_InitStructure;
  SPI_InitTypeDef SPI_InitStructure;
  NVIC_InitTypeDef NVIC_InitStructure;
  /* RPI_PORT Periph clock enable */
  RCC_AHB1PeriphClockCmd(RPI_GPIO_CLK, ENABLE);
  RPI_MODULE_CLK_SETUP(RPI_MODULE_CLK, ENABLE);
  /* RPI Port Configuration */
  GPIO_InitStructure.GPIO_Pin = RPI_SCK_PIN | RPI_MISO_PIN | RPI_MOSI_PIN | RPI_NSS_PIN;
  GPIO_InitStructure.GPIO_Speed =GPIO_Fast_Speed;
  GPIO_InitStructure.GPIO_Mode = GPIO_Mode_AF;
  GPIO_InitStructure.GPIO_OType = GPIO_OType_PP;
  GPIO_InitStructure.GPIO_PuPd = GPIO_PuPd_DOWN;
  GPIO_Init(RPI_PORT, &GPIO_InitStructure);

  GPIO_PinAFConfig(RPI_PORT, RPI_NSS_PINSOURCE, RPI_MODULE_AF);
  GPIO_PinAFConfig(RPI_PORT, RPI_SCK_PINSOURCE, RPI_MODULE_AF);
  GPIO_PinAFConfig(RPI_PORT, RPI_MISO_PINSOURCE, RPI_MODULE_AF);
  GPIO_PinAFConfig(RPI_PORT, RPI_MOSI_PINSOURCE, RPI_MODULE_AF);

  /* SPI configuration */
  SPI_I2S_DeInit(RPI_MODULE);
  SPI_InitStructure.SPI_Direction = SPI_Direction_2Lines_FullDuplex;
  SPI_InitStructure.SPI_DataSize = SPI_DataSize_8b;
  SPI_InitStructure.SPI_CPOL = SPI_CPOL_Low;
  SPI_InitStructure.SPI_CPHA = SPI_CPHA_1Edge;
  SPI_InitStructure.SPI_NSS = SPI_NSS_Hard;
  SPI_InitStructure.SPI_BaudRatePrescaler = SPI_BaudRatePrescaler_32;
  SPI_InitStructure.SPI_FirstBit = SPI_FirstBit_MSB;
  SPI_InitStructure.SPI_CRCPolynomial = 0x9B;
  SPI_InitStructure.SPI_Mode = SPI_Mode_Slave;
  SPI_Init(RPI_MODULE, &SPI_InitStructure);

  /* Configure RPI interrupt */
  NVIC_InitStructure.NVIC_IRQChannel = RPI_IRQ;
  NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority = 0;
  NVIC_InitStructure.NVIC_IRQChannelSubPriority = 1;
  NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE;
  NVIC_Init(&NVIC_InitStructure);

  /* Enable the Rx buffer not empty interrupt */
  SPI_I2S_ITConfig(RPI_MODULE, RPI_RX_IT, ENABLE);

  /* Enable the SPI peripheral */
  SPI_Cmd(RPI_MODULE, ENABLE);
}
