#include <stdio.h>
#include "event_manager.h"
#include "genericDrv.h"

int main(void){
  uint8_t buf[100];
  uint32_t n=100;

  event_type currEvent;

  int lval;
  int rval;
  unsigned char str[100];
  unsigned char val = 'a';
  for(int i=0;i<10;i++)
  {
    str[i]=val;
    lval=genericDrvIN_write(val++);
    //lval=genericDrvIN_write('\n');
    rval=EM_getEvent(&currEvent);
    printf("iteration %d: genDrv rval:%d, getEvent(): id:%d,type:%d\n",i,lval,rval,currEvent);
  }
  lval=genericDrvIN_write('\n');

  //retrieving events
  for(int i=0;i<10;i++)
  {
    rval=EM_getEvent(&currEvent);
    if(rval>=0)
      printf("it %d: got Event: id:%d type:%d\n",i,rval,currEvent);
    else
      printf("it %d: where is the event?\n",i);
  }
}
