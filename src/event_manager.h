#ifndef __EVENT_MANAGER_H
#define __EVENT_MANAGER_H

#include "stdint.h"

//application specific
//write the events used by program
typedef enum {
  generic_e,
  sysclk_e
} event_type;

#define EM_QUEUE_SIZE 50

int32_t EM_setup(void);
int32_t EM_getEvent(event_type *type);
int32_t EM_setEvent(event_type type);

#endif /* __EVENT_MANAGER_H */
