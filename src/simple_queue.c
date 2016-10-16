#include "simple_queue.h"

int ringBufPush(ringBug_t *q, uint8_t data){
  int next = q->head + 1;
  if(next >= q->maxlen)
    next = 0;
  //ring buffer full
  if(next == q->tail)
    return -1; //error
  q->buf[q->head] = data;
  q->head = next;
  return 0;
}
int ringBufPop(ringBug_t *q, uint8_t *data){
  //ring buffer empty
  if(q->head == q->tail)
    return -1;  //error
  *data = q->buf[q->tail];
  q->buf[q->tail] = 0; //optional
  int next = q->tail + 1;
  if(next >= q->maxlen)
    next=0;
  q->tail = next;
  return 0;
}
