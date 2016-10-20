#include "simple_queue.h"

/* Functions for 8-bit data */
int ringBufPush(ringBuf_t *q, uint8_t data){
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

int ringBufPop(ringBuf_t *q, uint8_t *data){
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

/* Functions for 32-bit data */
int ringBufPush32(ringBuf32_t *q, uint32_t data){
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

int ringBufPop32(ringBuf32_t *q, uint32_t *data){
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

int ringBufSPop32(ringBuf32_t *q, uint32_t *data){
  int next = q->tail + 1;
  if(next >= q->maxlen)
    next=0;
  //ring buffer empty
  if(q->head == next)
    return -1;  //error
  q->tail = next;
  *data = q->buf[next];
  q->buf[next] = 0; //optional
  return 0;
}
