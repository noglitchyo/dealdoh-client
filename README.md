# dealdoh-client
> A toy to deal DNS over HTTPS and more!

dealdoh-client is a simple DNS client embedding a DNS-over-HTTPS (DoH) proxy server and CLI to make & forward DNS queries through a variety of upstreams. 

![PHP from Packagist](https://img.shields.io/packagist/php-v/noglitchyo/dealdoh-client.svg)
[![Build Status](https://travis-ci.org/noglitchyo/dealdoh-client.svg?branch=master)](https://travis-ci.org/noglitchyo/dealdoh-client)
[![codecov](https://codecov.io/gh/noglitchyo/dealdoh-client/branch/master/graph/badge.svg)](https://codecov.io/gh/noglitchyo/dealdoh-client)
![Scrutinizer code quality (GitHub/Bitbucket)](https://img.shields.io/scrutinizer/quality/g/noglitchyo/dealdoh-client.svg)
![Packagist](https://img.shields.io/packagist/l/noglitchyo/dealdoh-client.svg)

## Description

dealdoh-client can be use in different manners and for different purposes:
- as a DoH proxy server
- as a DNS client, using the provided command-line client to make DNS queries
- both can use a pool of DNS upstream which can be easily configured by running some commands

Dealdoh is built on top of the [Dealdoh library](https://github.com/noglitchyo/dealdoh).

## Roadmap

- [ ] Dockerized application

## Getting started

As mentionned above, there is multiple ways to use dealdoh-client.
Let's see what can be done at the time with dealdoh-client.

### As a DoH proxy server

### As a DNS command-line client

#### Requirements

- PHP 7.3
- [Composer](https://getcomposer.org/doc/00-intro.md)

#### Installation

- You can use the client by cloning the project:

`git clone https://github.com/noglitchyo/dealdoh-client` 

`composer install`

- or by using as a dependency in a project:

`composer require noglitchyo/dealdoh-client` 

#### Usage

##### Add a DNS upstream

You can use the following command to add a DNS upstream to the DNS pool:

`php bin/dealdoh upstream:add https://dns.google.com/resolve google-doh-api`

##### Execute a DNS query

To execute DNS query directly from the command-line, you can use the provided binary:

`php bin/dealdoh resolve tools.ietf.org AAAA --pretty`

It will output the result as JSON string: (response is truncated)
```json
{
    "header": {
        "id": 0,
        "qr": true,
        "opcode": 0,
        "aa": false,
        "tc": false,
        "rd": true,
        "ra": true,
        "z": 0,
        "rcode": 0
    },
    "question": [
        {
            "qname": "tools.ietf.org.",
            "qtype": 28,
            "qclass": 1
        }
    ],
    "answer": [
        {
            "name": "tools.ietf.org.",
            "type": 28,
            "class": 1,
            "ttl": 13,
            "data": "2001:1900:3001:11::3e"
        }
    ],
    "authority": [],
    "additional": []
}
```

## Testing

If you wish to run the test, checkout the project and run the test with: 

`composer test`

## Contributing

Get started here [CONTRIBUTING.md](CONTRIBUTING.md).

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
