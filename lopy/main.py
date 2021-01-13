from network import LoRa
from machine import Pin

import socket
import time
import ubinascii
import pycom

pycom.heartbeat(False)
pycom.rgbled(0xff0000)
# Initialise LoRa in LORAWAN mode.
lora = LoRa(mode=LoRa.LORAWAN, region=LoRa.EU868)

USERID = "41159327-551b-11eb-a953-246eba5ea628"
VALUE = "69"

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
