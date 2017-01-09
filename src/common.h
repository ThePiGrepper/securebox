#ifndef __COMMON_H
#define __COMMON_H

#include "gprsDrv.h"
#include "gpsDrv.h"
#include "wifiDrv.h"
#include "rpiDrv.h"
#include "genericDrv.h"
#include "stm32f4xx.h"

#define LOCK_ON GPIO_SetBits(LOCK_PORT, LOCK_PIN)
#define LOCK_OFF GPIO_ResetBits(LOCK_PORT, LOCK_PIN)

typedef struct{
  unsigned char data;
  unsigned char sent;
  unsigned char enabled;
} msgPipe;

//Functions
void HW_setup(void);
void Delay(volatile uint32_t nTime);
void TimingDelay_Decrement(void);
void waitforit(int32_t timeout);
void proto_main(void);

#endif /* __COMMON_H */
