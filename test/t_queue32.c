#include "simple_queue.h"
#include <stdio.h>

int main(void){
  uint32_t buf32[100];
  uint32_t n=100;
  ringBuf32_t tqueue32={buf32,0,0,100};
  uint32_t c='a';
  for(uint8_t i=0;i<n;i++)
    if(!ringBufPush32(&tqueue32,c++))
      printf("S:%d\n",c-1);
    else
      printf("F:%d\n",c-1);
  for(uint8_t i=0;i<n;i++)
    if(!ringBufPop32(&tqueue32,&c))
      printf("S:%d\n",c);
    else
      printf("F:%d\n",c);
}
