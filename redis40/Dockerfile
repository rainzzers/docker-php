# Redis 4.0.9 构建
# 背景：
# 1. Docker Hub 官方镜像无 4.0.9 tag，且当前网络直连 Docker Hub 超时
# 2. redis50-base:local = 运行中 redis50 容器 docker commit 出来的完整底座
#    （debian bullseye / glibc 2.31），apt 可装 gcc/make
#    （ca-certificates/wget 因 bullseye 过 LTS 期 404，源码包由 fetcher 阶段代下）
# 3. 在底座内编译，产物与运行环境 glibc 完全一致
#    （曾试过 golang:latest 多阶段编译，glibc 2.36 产物在 2.31 底座上 exec 报 not found）
FROM golang:latest AS fetcher

RUN wget -q -O /redis-4.0.9.tar.gz http://download.redis.io/releases/redis-4.0.9.tar.gz

FROM redis50-base:local

COPY --from=fetcher /redis-4.0.9.tar.gz /tmp/redis-4.0.9.tar.gz

# 编译依赖装完即卸，源码包编译后清理，保持镜像接近底座大小
# 源切换：bullseye 已过 LTS，deb.debian.org 安全池部分包 404，切 archive.debian.org 冻结归档
#   （归档不再更新 Release，须关 Valid-Until 检查）
# libc6-dev 必须显式装：它是 gcc 的 Recommends 而非硬依赖，--no-install-recommends 会跳过（缺 stdlib.h）
# Acquire::Retries：外网偶发连接中断，apt 自动重试
RUN set -eux; \
    sed -i 's|deb.debian.org|archive.debian.org|g; s|security.debian.org|archive.debian.org|g' /etc/apt/sources.list; \
    if [ -d /etc/apt/sources.list.d ]; then sed -i 's|deb.debian.org|archive.debian.org|g; s|security.debian.org|archive.debian.org|g' /etc/apt/sources.list.d/* || true; fi; \
    apt-get -o Acquire::Check-Valid-Until=false -o Acquire::Retries=8 update; \
    apt-get -o Acquire::Retries=8 install -y --no-install-recommends gcc make libc6-dev; \
    cd /tmp; \
    tar xzf redis-4.0.9.tar.gz; \
    cd redis-4.0.9; \
    make; \
    make install; \
    cd /; \
    rm -rf /tmp/redis-4.0.9 /tmp/redis-4.0.9.tar.gz; \
    apt-get purge -y gcc make; \
    apt-get autoremove -y; \
    rm -rf /var/lib/apt/lists/*

WORKDIR /data

EXPOSE 6379

CMD ["redis-server"]
