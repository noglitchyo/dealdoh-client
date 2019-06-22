## dealdoh-client with Docker

![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/noglitchyo/dealdoh-client.svg)

[dealdoh-client](https://cloud.docker.com/repository/docker/noglitchyo/dealdoh-client) is available as Docker image.
It comes with a PHP-FPM 7.3 and dealdoh-client sources.

### Getting started

Start the container with:

`docker run -it --name dealdoh-client --rm noglitchyo/dealdoh-client`

### Usage

#### As FPM upstream

You can configure your web server to use the dealdoh-client container as an upstream.
Default PHP port 9000 is exposed.

#### Use the CLI

`docker exec dealdoh-client bin/dealdoh <your command>`

