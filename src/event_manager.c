#include "event_manager.h"
#include "simple_queue.h"

typedef struct {
  int32_t id;
  event_type type;
} event_t;

static event_t event_pool[EM_QUEUE_SIZE];
static uint32_t event_buffer[EM_QUEUE_SIZE];
static uint32_t poolCount=0;
static ringBuf32_t event_queue = {event_buffer,0,EM_QUEUE_SIZE-1,EM_QUEUE_SIZE};

/*Function Implementations */
static int32_t event_ID=0;
static int32_t get_id(void){
  return event_ID++;
}

int32_t EM_setup(void){
  return 0;
}

int32_t EM_getEvent(event_type *type){
  uint32_t count;
  if(ringBufSPop32(&event_queue,&count))
  {
    return -1;
  }
  *type=event_pool[count].type;
  return event_pool[count].id;
}
  

int32_t EM_setEvent(event_type type){
  if(ringBufPush32(&event_queue,poolCount))
  { //event queue full. send some external warning?
    return -1;
  }
  int32_t id=get_id();
  event_pool[poolCount].id = id;
  event_pool[poolCount++].type = type;
  if(poolCount>=EM_QUEUE_SIZE)
    poolCount=0;
  return id;
}
