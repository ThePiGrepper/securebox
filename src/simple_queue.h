#ifndef __SIMPLE_QUEUE_H
#define __SIMPLE_QUEUE_H

#include <stdint.h>

typedef struct
{
  uint8_t * const buf;
  int head;
  int tail;
  const int maxlen;
} ringBug_t;

int ringBufPush(ringBug_t *q, uint8_t data);
int ringBufPop(ringBug_t *q, uint8_t *data);

#endif /* __SIMPLE_QUEUE_H */
