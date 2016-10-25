#include "genericDrv.h"
#include "simple_queue.h"

static uint8_t dinBuf[GDRV_BUFIN_SZ*2];
static ringBuf_t dinBufCtrl = {dinBuf,0,0,GDRV_BUFIN_SZ};
static const uint32_t dinBufNext= (uint32_t)dinBuf + (uint32_t)GDRV_BUFIN_SZ;

static genericDrvIN_frame doutPool[GDRV_BUFOUT_SZ]={0};
static uint32_t doutBuf[GDRV_BUFOUT_SZ];
static uint32_t poolCount=0;
static ringBuf32_t doutBufCtrl = {doutBuf,0,GDRV_BUFOUT_SZ-1,GDRV_BUFOUT_SZ};

static uint32_t currLen=0;
static uint8_t *currStr=dinBuf;

/*Function Implementations */

int32_t genericDrvIN_read(genericDrvIN_frame **addr){
  uint32_t count;
  //release dinBuf
  uint8_t *str = doutPool[doutBufCtrl.tail].str;
  uint32_t len = doutPool[doutBuf[doutBufCtrl.tail]].len;
  dinBufCtrl.tail = (((uint32_t)str + len - dinBufNext + 1) >= 0)?
    (uint32_t)str + len - dinBufNext + 1 : (uint32_t)str + len - (uint32_t)dinBuf + 1;
  if(ringBufSPop32(&doutBufCtrl,&count)) return -1; //OUT buffer empty
  *addr = (genericDrvIN_frame *) &doutPool[count];
  return 0;
}

int32_t genericDrvIN_write(uint8_t data){
  if(ringBufPush(&dinBufCtrl,data)) return -1; //IN buffer full
  //writes out of boundary to enable regular string manipulation
  if((uint32_t)(currStr + currLen) >= dinBufNext)
    dinBuf[(int)(currStr-dinBuf) + currLen] = data;
  if(data == '\n') //send frame to buffer
  {
    if(ringBufPush32(&doutBufCtrl,poolCount))
    {
      dinBufCtrl.head = currStr-dinBuf; //flush incomplete frame
      currLen = 0;
      return -2; //OUT buffer full
    }
    doutPool[poolCount].str = currStr;
    doutPool[poolCount++].len = currLen;
    if(poolCount >= GDRV_BUFOUT_SZ)
      poolCount = 0;
    currStr = dinBuf + dinBufCtrl.head;
    currLen = 0;
  }
  else
  {
    currLen++;
  }
  return 0;
}
