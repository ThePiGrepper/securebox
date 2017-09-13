#ifndef __SECUREBOX_H
#define __SECUREBOX_H

#include "common.h"
#include "gprsDrv.h"
#include "gpsDrv.h"
#include "wifiDrv.h"
#include "rpiDrv.h"
#include "accgyroDrv.h"
#include "genericDrv.h"
#include "event_manager.h"

//Functions
void HW_setup(void);
void proto_main(void);
#endif /* __SECUREBOX_H */
