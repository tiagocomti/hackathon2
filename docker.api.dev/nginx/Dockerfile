ARG NGINX_VERSION=1.26.2
FROM --platform=linux/amd64 nginx:${NGINX_VERSION} as build

RUN apt-get update && \
    apt-get install -y \
        openssh-client \
	zip \
        wget \
        libxml2 \
        libxslt1-dev \
        libpcre3 \
        libpcre3-dev \
        zlib1g \
        zlib1g-dev \
        openssl \
        libssl-dev \
        libtool \
        automake \
        gcc \
        g++ \
        make && \
    rm -rf /var/cache/apt

RUN wget "http://nginx.org/download/nginx-${NGINX_VERSION}.tar.gz" && \
    tar -C /usr/src -xzvf nginx-${NGINX_VERSION}.tar.gz

WORKDIR /src/
RUN wget "https://github.com/atomx/nginx-http-auth-digest/archive/refs/heads/master.zip"
RUN unzip master.zip


WORKDIR /usr/src/nginx-${NGINX_VERSION}
RUN NGINX_ARGS=$(nginx -V 2>&1 | sed -n -e 's/^.*arguments: //p') \
    ./configure --with-compat --add-dynamic-module=/src/nginx-http-auth-digest-master ${NGINX_ARGS} && \
    make modules

FROM --platform=linux/amd64 nginx:${NGINX_VERSION}

COPY ./nginx/nginx.conf /etc/nginx/nginx.conf

COPY --from=build /usr/src/nginx-${NGINX_VERSION}/objs/ngx_http_auth_digest_module.so /etc/nginx/modules
COPY ./nginx/dhparams.pem /etc/nginx/
COPY ./nginx/certificate.crt /etc/nginx/
COPY ./nginx/certificate.key /etc/nginx/
COPY ./nginx/swagger.digest /etc/nginx/

EXPOSE 443



