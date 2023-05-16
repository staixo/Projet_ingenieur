FROM debian

WORKDIR /usr/src/

ADD . ./

RUN apt-get update && apt-get install -y vim && apt-get install -y python3 && apt-get install -y python3-pip && pip3 install --no-cache-dir -r requirements.txt