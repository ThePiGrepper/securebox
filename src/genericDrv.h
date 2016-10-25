#ifndef __GENERIC_DRV_H
#define __GENERIC_DRV_H
#include <stdint.h>

typedef struct {
  uint8_t *str;
  uint32_t len;
} genericDrvIN_frame;

#define GDRV_BUFIN_SZ 200
#define GDRV_BUFOUT_SZ 200

int32_t genericDrvIN_read(genericDrvIN_frame **addr);
int32_t genericDrvIN_write(uint8_t data);

#endif /* __GENERIC_DRV_H */

