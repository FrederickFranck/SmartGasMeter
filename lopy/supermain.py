from network import LoRa
from machine import Pin

import socket
import time
import ubinascii
import pycom

pycom.heartbeat(False)

##############
#WAKE UP RPI#
#############




########################
#RECIEVE DATA FROM RPI#
#######################
bit0 = Pin('P23', mode = Pin.IN)
bit1 = Pin('P22', mode = Pin.IN)
bit2 = Pin('P21', mode = Pin.IN)
bit3 = Pin('P20', mode = Pin.IN)
bit4 = Pin('P19', mode = Pin.IN)
bit5 = Pin('P18', mode = Pin.IN)
bit6 = Pin('P17', mode = Pin.IN)
control = Pin('P16', mode = Pin.IN)
control2 = Pin('P15', mode = Pin.IN)

value = 0

while(control() != 1 or control2() != 1):
    continue
if(bit6() == 1):
    value += 64
if(bit5() == 1):
    value += 32
if(bit4() == 1):
    value += 16
if(bit3() == 1):
    value += 8
if(bit2() == 1):
    value += 4
if(bit1() == 1):
    value += 2
if(bit0() == 1):
    value += 1

VALUE = str(value)


###############
#SHUTDOWN RPI#
##############


#############
#READ USERID#
#############

file = read('id')
USERID = file.read()
USERID = USERID[:(len(USERID) - 1)]
file.close()


#####################
#SEND DATA VIA LORA#
####################

pycom.rgbled(0xff0000)
# Initialise LoRa in LORAWAN mode.
lora = LoRa(mode=LoRa.LORAWAN, region=LoRa.EU868)

#USERID = "41159327-551b-11eb-a953-246eba5ea628"
#VALUE = "69"

# create an OTAA authentication parameters, change them to the provided credentials
app_eui = ubinascii.unhexlify('70B3D57ED003B849')
app_key = ubinascii.unhexlify('***')

# join a network using OTAA (Over the Air Activation)
lora.join(activation=LoRa.OTAA, auth=(app_eui, app_key), timeout=0)

# wait until the module has joined the network
while not lora.has_joined():
    time.sleep(2.5)
    print('Not yet joined...')

print('Joined')
pycom.rgbled(0xff00)

# create a LoRa socket
s = socket.socket(socket.AF_LORA, socket.SOCK_RAW)

# set the LoRaWAN data rate
s.setsockopt(socket.SOL_LORA, socket.SO_DR, 5)

# make the socket blocking
# (waits for the data to be sent and for the 2 receive windows to expire)
s.setblocking(True)

# send data
pycom.rgbled(0x0000ff)
s.send(str("{{\"userid\":\"{}\",\"value\":\"{}\"}}".format(USERID,VALUE)).encode())
pycom.rgbled(0x000000)

# make the socket non-blocking
# (because if there's no data received it will block forever...)
s.setblocking(False)



###############
#GO TO SLEEP #
##############
