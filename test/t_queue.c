#include "simple_queue.h"
#include <stdio.h>

int main(void){
  uint8_t buf[100];
  uint32_t n=100;
  ringBuf_t tqueue={buf,0,0,100};
  uint8_t c='a';
  for(uint8_t i=0;i<n;i++)
    if(!ringBufPush(&tqueue,c++))
      printf("S:%d\n",c-1);
    else
      printf("F:%d\n",c-1);
  for(uint8_t i=0;i<n;i++)
    if(!ringBufPop(&tqueue,&c))
      printf("S:%d\n",c);
    else
      printf("F:%d\n",c);
}
