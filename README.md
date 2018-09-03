## RaspberryPI pollaio controller

### Needed HW:

1x Raspberry Pi B (at least) board

1x 4 Relay Board

1x Raspi Motor Hat

1x 8 Gb Good Quality MicroSD card

1x wide angle Raspicam

1x DC / Stepper motor

(optional) 1x Inline powerbank (able to be charged while delivering usb power to act as a UPS)

### Software:

Raspbian OS

Apache + PHP7 + MariaDB

### Configuration:

From the power outlet, connect a phone charger to the powerbank and the RPI to the powerbank.

The RaspiMotorHAT works with both DC or Stepper motors, check documentation to correct my script to your needings.

The Relay board will be connected to GPIO over the HAT.

The Raspicam have to be connected to his special connector on the board.

### Goal of the project:

to have remote control with the motor to open and close the chicken house door, with the relay to switch on or off lights and incubator remotely and with the wide angle raspicam to check everything from your sofa.
