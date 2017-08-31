#ifndef __ACCGYRO_DRV_H
#define __ACCGYRO_DRV_H
#include <stdint.h>

typedef struct {
  int str;
  uint32_t len;
} accgyroDrvIN_frame;

#define ACCGYRODRV_BUFIN_SZ 1000
#define ACCGYRODRV_BUFOUT_SZ 500

#define ACCGYRO_SCL_SPEED 30000

void accgyroParse(uint8_t data);
int32_t accgyroDrvIN_read(uint8_t **ptr);
int32_t accgyroDrvIN_write(uint8_t data);
void accgyroDrv_Setup(void);

#endif /* __ACCGYRO_DRV_H */
