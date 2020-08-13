import socket
from struct import unpack
from socket import inet_aton
import threading
import time
import math

class Checker(threading.Thread):
	def __init__(self, list:list, type:int):
		threading.Thread.__init__(self)
		self.list = list
		self.type = type
	
	def run(self):
		for proxy in self.list:
			time.sleep(0.1)
			if proxy == None:
				continue
			
			print("-> {}".format(proxy))
			
			if ":" in proxy:
				proxyData = proxy.split(":")
			else:
				proxyData = [None] * 2
				proxyData[0] = proxy
				proxyData[1] = 3128
			
			for i in range(0, 2):
				# Fin du support des proxys HTTP
				if self.type == 0:
					break
                
				client = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
				client.settimeout(5)
				try:
					client.connect((str(proxyData[0]), int(proxyData[1])))
				except:
					break
				
				uri = "HEAD /?a={a}&b={b}&c={c}&d={d}&e={e} HTTP/1.0\r\nHost: 90.63.25.246\r\nCookie: tk=2afa6130500e10ce3b9ce7eeed4794083ae5d4ac\r\n\r\n".format(a=proxyData[0], b=int(proxyData[1])*1337, c=self.type, d=i, e=time.time())
				
				if self.type == 0:
					if i == 0:
						client.send(bytes(uri, "utf-8"))
						try:
							rep = client.recv(1024)
							
						except:
							break
					elif i == 1:
						client.send(b"CONNECT 90.63.25.246 HTTP/1.0\r\n\r\n")
						try:
							rep = client.recv(1024)
						except:
							break
						
						if "200 OK" in str(rep):
							client.send(bytes(uri, "utf-8"))
							try:
								rep = client.recv(1024)
								
							except:
								break
				elif self.type == 1:
					client.send(b"\x05\x01\x00")
					try:
						rep = client.recv(32)
					except:
						break
					
					if b"\x05\00" not in rep:
						break
					
					client.send(b"\x05\x01\x00\x03\x0c90.63.25.246\x00\x50")
					try:
						rep = client.recv(32)
					except:
						break
					
					client.send(bytes(uri, "utf-8"))
					try:
						rep = client.recv(1024)
						
					except:
						break
				
				if self.type == 1:
					client.close()
					break

def partition(list:list, parts:int) -> list:
	length = len(list)
	finalList = []
	
	for i in range(0, length):
		if list[i] not in finalList:
			finalList.append(list[i])
	
	finalLength = len(finalList)
	result = [None] * parts
	size = math.ceil(len(finalList)/parts)
	nb = 0
	
	for i in range(0, parts):
		result[i] = [None] * size
		for j in range(0, size):
			if nb >= finalLength:
				break
			
			result[i][j] = finalList[nb]
			nb += 1
	
	return result



client = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
client.connect(("90.63.25.246", 80))
client.send(b"GET /?purge-opx234 HTTP/1.0\r\nHost: 90.63.25.246\r\n\r\n");
client.close()

threads = []
nb = 0;
files = ["http.txt", "socks5.txt"]

for id, file in enumerate(files):
	if file == "http.txt":
		continue
	
	print("Appel calcul fichier ID {}".format(id))
	file = open(file, "r").read()
	file = file.replace("\r", "\n").replace("\n\n", "\n")
	
	proxys = partition(file.split("\n"), 10)
	for list in proxys:
		print("Appel creation thread fichier ID {}".format(id))
		if list == None:
			continue
		
		thread = Checker(list, id)
		threads.append(thread)
		nb += 1
		print("Thread {}".format(nb))

for thread in threads:
	thread.start()

for thread in threads:
	thread.join()