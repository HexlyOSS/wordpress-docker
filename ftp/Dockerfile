FROM golang

RUN go get github.com/goftp/server/exampleftpd
WORKDIR /go/src/github.com/goftp/server/exampleftpd
RUN go build -o exampleftpd
RUN mkdir -p /opt/root

EXPOSE 2121

CMD ["exampleftpd", "-root", "/opt/root", "-host", "0.0.0.0"]