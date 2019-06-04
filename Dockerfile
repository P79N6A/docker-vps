FROM ubuntu:latest
MAINTAINER      X.Minamoto "xuyuan8720@189.cn"

ENV 			DEBIAN_FRONTEND noninteractive

EXPOSE		1920-1929 6880-6888 
#tcp：1920-sss, 1921-ftp, 1922-ssh,1923-kod,1925-mlnet,1926-deluge 1927-v2ray
#tcp/udp：6885-6888-deluge, 6880-ed/tcp, 6884-ed/udp, 6881,6882-bt, 6883-dht/udp

RUN			/usr/bin/apt-get -y update; \
				/usr/bin/apt-get -y upgrade; \
				/usr/bin/apt-get -y install wget curl apt-utils net-tools nano tzdata ssh deluge-web deluged mldonkey-server golang-cfssl; \
				/usr/bin/apt-get -y autoremove; \
				/usr/bin/apt-get -y clean; \
				/usr/bin/apt-get -y autoclean; \
				rm -rfv /tmp/*

COPY			buildfiles /root/
# COPY			lampp /opt/

RUN			/bin/echo 'root:administratorishere' |chpasswd;useradd -m xy;/bin/echo 'xy:iamlegal' |chpasswd; \
				ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime; \
				echo "Asia/Shanghai" > /etc/timezone; \
				dpkg-reconfigure -f noninteractive tzdata; \
				/bin/echo "net.ipv4.ip_forward=1">>/etc/sysctl.conf; \
				/bin/echo 'Port 1922' >> /etc/ssh/sshd_config; \
				/bin/echo 'PermitRootLogin yes' >> /etc/ssh/sshd_config; \
				/bin/echo 'export PATH=$PATH:/root/bin'>> /root/.bashrc; \
				/bin/echo 'export PATH=$PATH:/root/svr'>> /root/.bashrc; \
				mkdir -p /root/.config/deluge; cp /root/deluge/*.conf /root/.config/deluge; \
				mkdir -p /home/xy/.config/deluge; cp /root/deluge/*.conf /home/xy/.config/deluge; \
				mkdir -p /home/incoming; chown xy:xy /home/incoming; chmod 777 /home/incoming; \
				mkdir -p /home/xy/.mldonkey; cp -rf /root/.mldonkey /home/xy; chown -R xy:xy /home/xy; \
				/root/bin/installv2ray.sh
				

# CMD			"chown -R xy:xy /home/xy/.mldonkey"

# ENTRYPOINT	"/root/bin/start.sh"
