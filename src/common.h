#ifndef __COMMON_H
#define __COMMON_H
#include <stdint.h>

typedef struct{
  unsigned char data;
  unsigned char sent;
  unsigned char enabled;
} msgPipe;

void common_Setup(void);
void LOCK_ON(void);
void LOCK_OFF(void);
void SYSTEM_ON(void);
void SYSTEM_OFF(void);
void Delay(volatile uint32_t nTime);
void TimingDelay_Decrement(void);
void waitforit(int32_t timeout);

#endif /* __COMMON_H */
