#include "accgyroDrv.h"
#include "settings.h"
#include "simple_queue.h"
#include "event_manager.h"

static uint8_t dinBuf[ACCGYRODRV_BUFIN_SZ*2];
static ringBuf_t dinBufCtrl = {dinBuf,0,0,ACCGYRODRV_BUFIN_SZ};

static rpiDrvIN_frame doutPool[ACCGYRODRV_BUFOUT_SZ]={0};
static uint32_t doutBuf[ACCGYRODRV_BUFOUT_SZ]={0};
static uint32_t poolCount=0;
static ringBuf32_t doutBufCtrl = {doutBuf,0,ACCGYRODRV_BUFOUT_SZ-1,ACCGYRODRV_BUFOUT_SZ};

static uint32_t currLen=0;
static int currStr=0;

/*Function Implementations */

static int32_t getINBufFreeSpace(void)
{
  return ((dinBufCtrl.tail-dinBufCtrl.head) > 0)?
    dinBufCtrl.tail-dinBufCtrl.head-1:ACCGYRODRV_BUFIN_SZ+(dinBufCtrl.tail-dinBufCtrl.head)-1;
}

//parse AccGyro data stream.
//gets data from GPRMC data
//sends to buffer only
static uint8_t parse_count = 0;
void accgyroParse(uint8_t data){
  if(parse_count < 2)
  {
    if(data == '$')
      parse_count++;
    else
      parse_count=0;
  }
  else
  {
    accgyroDrvOUT_write(data);
    if(data == '\n')
      parse_count=0;
  }
}

int32_t accgyroDrvIN_read(uint8_t **ptr){
  uint32_t count;
  int str = doutPool[doutBuf[doutBufCtrl.tail]].str;
  uint32_t len = doutPool[doutBuf[doutBufCtrl.tail]].len;
  if(ringBufSPop32(&doutBufCtrl,&count)) return -1; //OUT buffer empty
  //release dinBuf
  dinBufCtrl.tail = ((str + (int)len + 1 - ACCGYRODRV_BUFIN_SZ) >= 0)?
    str + len + 1 - ACCGYRODRV_BUFIN_SZ : str + len + 1;
  *ptr = &dinBuf[doutPool[count].str];
  return doutPool[count].len;
}

int32_t accgyroDrvOUT_write(uint8_t data){
  if(ringBufPush(&dinBufCtrl,data)) return -1; //IN buffer full
  //writes out of boundary to enable regular string manipulation
  if((currStr + currLen) >= ACCGYRODRV_BUFIN_SZ)
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
    if(poolCount >= ACCGYRODRV_BUFOUT_SZ)
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

void accgyroDrv_Setup(void){
  /* buffers startup values */
  doutBuf[ACCGYRODRV_BUFOUT_SZ-1] = ACCGYRODRV_BUFOUT_SZ-1;
  doutPool[ACCGYRODRV_BUFOUT_SZ-1].str = ACCGYRODRV_BUFIN_SZ-1;
  doutPool[ACCGYRODRV_BUFOUT_SZ-1].len = 0;
  /* Hardware setup */
  GPIO_InitTypeDef GPIO_InitStructure;
  I2C_InitTypeDef I2C_InitStructure;
  NVIC_InitTypeDef NVIC_InitStructure;

  /* ACCGYRO I2C Periph clock enable */
  ACCGYRO_MODULE_CLK_SETUP(ACCGYRO_MODULE_CLK, ENABLE);
  /* ACCGYRO Port (SDA & SCL GPIO) clock enable */
  RCC_AHB1PeriphClockCmd(ACCGYRO_GPIO_CLK, ENABLE);
  /* ACCGYRO Port Configuration */
  GPIO_InitStructure.GPIO_Pin = ACCGYRO_SCL_PIN | ACCGYRO_SDA_PIN;
  GPIO_InitStructure.GPIO_Speed = GPIO_Low_Speed; //GPIO output speed
  GPIO_InitStructure.GPIO_Mode = GPIO_Mode_AF;
  GPIO_InitStructure.GPIO_OType = GPIO_OType_OD;
  GPIO_InitStructure.GPIO_PuPd = GPIO_PuPd_UP;

  GPIO_PinAFConfig(ACCGYRO_PORT, ACCGYRO_SDA_PINSOURCE, ACCGYRO_MODULE_AF);
  GPIO_PinAFConfig(ACCGYRO_PORT, ACCGYRO_SCL_PINSOURCE, ACCGYRO_MODULE_AF);
  GPIO_Init(ACCGYRO_PORT, &GPIO_InitStructure);

  /* I2C configuration */
  I2C_DeInit(ACCGYRO_MODULE);
  I2C_InitStructure.I2C_ClockSpeed = ACCGYRO_SCL_SPEED;
  I2C_InitStructure.I2C_Mode = I2C_Mode_I2C;
  I2C_InitStructure.I2C_DutyCycle = I2C_DutyCycle_2;
  I2C_InitStructure.I2C_OwnAddress1 = 0xEE;  // SLAVE ADDRESS
                                             //read the example, still.. nfi
  I2C_InitStructure.I2C_Ack = I2C_Ack_Enable;
  I2C_InitStructure.I2C_AcknowledgedAddress = I2C_AcknowledgedAddress_7bit; // Acknowledged address.
  I2C_Init(ACCGYRO_MODULE, &I2C_InitStructure);

  /* Configure ACCGYRO interrupt */
  NVIC_InitStructure.NVIC_IRQChannel = ACCGYRO_IRQ;
  NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority = 1;
  NVIC_InitStructure.NVIC_IRQChannelSubPriority = 0;
  NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE;
  NVIC_Init(&NVIC_InitStructure);

  /* Enable the Rx buffer not empty interrupt */
  I2C_ITConfig(ACCGYRO_MODULE, I2C_IT_BUF| I2C_IT_EVT, ENABLE);

  /* Enable the I2C peripheral */
  I2C_Cmd(ACCGYRO_MODULE, ENABLE);
}
