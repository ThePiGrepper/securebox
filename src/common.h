#ifndef __COMMON_H
#define __COMMON_H

#include "gprsDrv.h"
#include "gpsDrv.h"
#include "wifiDrv.h"
#include "genericDrv.h"
#include "stm32f4xx.h"

#define LOCK_ON GPIO_SetBits(GPIOA, GPIO_Pin_5)
#define LOCK_OFF GPIO_ResetBits(GPIOA, GPIO_Pin_5)

//Functions
void HW_setup(void);
void proto_main(void);

#endif /* __COMMON_H */
