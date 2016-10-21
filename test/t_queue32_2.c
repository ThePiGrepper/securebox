/*testing special queue pops */

#include "simple_queue.h"
#include <stdio.h>
#define BSIZE 5

int main(void){
  uint32_t buf32[BSIZE];
  uint32_t n=BSIZE;
  //set tail init position to last
  ringBuf32_t tqueue32={buf32,0,BSIZE-1,BSIZE};
  uint32_t c=100;
  uint32_t r;
  //test normal operation(success)
  if(ringBufPush32(&tqueue32,c++))
    printf("error: success expected.\n");
  printf("status: head=%d,tail=%d\n",tqueue32.head,tqueue32.tail);
  if(ringBufSPop32(&tqueue32,&r))
    printf("error: success expected.\n");
  printf("status: head=%d,tail=%d\n",tqueue32.head,tqueue32.tail);
  //if(r != (c-1))
    printf("error: value mismatch: read %d , expected %d.\n",r,c-1);
  //test normal operation(failure)
  if(!ringBufSPop32(&tqueue32,&r))
    printf("error: failure expected.\n");
  printf("status: head=%d,tail=%d\n",tqueue32.head,tqueue32.tail);
  if(ringBufPush32(&tqueue32,c++))
    printf("error: success expected.\n");
  printf("status: head=%d,tail=%d\n",tqueue32.head,tqueue32.tail);
  //get last added value
  if(ringBufSPop32(&tqueue32,&r))
    printf("error: success expected.\n");
  printf("status: head=%d,tail=%d\n",tqueue32.head,tqueue32.tail);
  //if(r != (c-1))
    printf("error: value mismatch: read %d , expected %d.\n",r,c-1);
  //assumption: queue empty
  //test
  if(!ringBufSPop32(&tqueue32,&r))
    printf("error: failure expected.\n");
  printf("status: head=%d,tail=%d\n",tqueue32.head,tqueue32.tail);
  if(!ringBufSPop32(&tqueue32,&r))
    printf("error: failure expected.\n");
  printf("status: head=%d,tail=%d\n",tqueue32.head,tqueue32.tail);
  //test that last value is saved.
  //printf("status: head=%d,tail=%d\n",tqueue32.head,tqueue32.tail);
  for(uint8_t i=0;i<BSIZE;i++)
  {
    int rv=ringBufPush32(&tqueue32,c++);
    printf("status: wval:%d, rval:%d, iteration:%d,head=%d,tail=%d\n",c-1,rv,i,tqueue32.head,tqueue32.tail);
  }
  for(uint8_t i=0;i<BSIZE;i++)
  {
    int rv=ringBufSPop32(&tqueue32,&r);
    printf("status: read:%d, rval:%d, iteration:%d,head=%d,tail=%d\n",r,rv,i,tqueue32.head,tqueue32.tail);
  }
  return 0;
}
