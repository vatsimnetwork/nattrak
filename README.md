<p align="center"><a href="https://nattrak.vatsim.net" target="_blank"><img src="https://github.com/vatsimnetwork/nattrak/blob/1ffe41ca2087844dab75cb0b33ed9f85f96a8c1c/public/images/natTrak_Logo_2000px.png?raw=true" width="200"></a></p>

[//]: # (<p align="center">)

[//]: # (<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>)

[//]: # (<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>)

[//]: # (<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>)

[//]: # (<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>)

[//]: # (</p>)

## About natTrak

natTrak is the web application used by the Atlantic oceanic controll areas of the [VATSIM](https://vatsim.net) network. Pilots submit oceanic clearance requests via the website, and controllers respond to those requests.

This project is maintained by the VATSIM Tech Team.

natTrak is a [Laravel](https://laravel.com) application using Bootstrap 5.3, Livewire 3.0, and AlpineJS. It also makes use of Soketi for websocket support.

### Primary Developers

- [Liesel Downes](https://github.com/lieselwd)
- [William McKinnerney](https://github.com/williammck)

### FAQs

#### How do I request new features?

Create an Issue in this repository, or let one of us know via Discord if you're unable to. All ideas welcome!

#### Is natTrak compatible with Hoppies CPDLC?

Unfortunately it is not and there are no plans for this. 

#### Is there an API?

Yes! The endpoints are:

- /api/clx-messages - Detailed oceanic clearance messages 
- /api/plugins-rcl - Simple oceanic clearance _request_ messages
- /tracks - the tracks currently in use by natTrak.

It is highly recommended that you use the clx-messages endpoint. In future more endpoints will be available.
