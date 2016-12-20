#ifndef __RPI_DRV_H
#define __RPI_DRV_H
#include <stdint.h>

typedef struct {
  int str;
  uint32_t len;
} rpiDrvIN_frame;

#define RPIDRV_BUFIN_SZ 1000
#define RPIDRV_BUFOUT_SZ 500

void rpiParse(uint8_t data);
int32_t rpiDrvIN_read(uint8_t **ptr);
int32_t rpiDrvIN_write(uint8_t data);
void rpiDrv_Setup(void);

#endif /* __RPI_DRV_H */
