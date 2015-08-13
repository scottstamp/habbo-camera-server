# Habbo Server Camera
PhP Class, to Emulate Sulake's Habbo Hotel "in-game" Camera Applet.

### About

#### Author
> Claudio A. Santoro W.

#### Version
> 1.0 Alpha

#### License
> Copyright (C) 2015 Sulake Corporation Oy

> This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

> This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

> *Read Complete GNU GPL 3.0 [Clicking Here](https://github.com/sant0ro/habbo-camera-server/blob/master/LICENSE.md)*

### Installation
- Download ServerCamera.php
- Put in your WebServer
- Run First Time to The Script Create the Folders. *(putting: servercamera.php?install=true)*
- Enjoy.

#### Test if ServerCamera is Working
> You can Use The Test jSON for test ServerCamera. *(putting servercamera.php?test=true)*

#### Assets
> You need Habbo's Assets to Make the ServerCamera generate the Images. All Assets are available [here](https://github.com/sant0ro/habbo-asset-extractor)
> **Attention**: Assets will be downloaded in correct folders. You Need Execute assets.php, figures.php, masks.php

#### Default Folders
- Default Folder for Mask Assets: /masks/
- Default Folder for Sprites Assets: /sprites/
- Default Folder for Generated Image: /servercamera/purchased/

#### Pre-Requisites
- Need PhP 5.4x or Later
- GD Library of PhP
- HTTP Server (Commonly Web Server)
- The Following Mime-Types
  - Image/PNG
  - Text/Javascript
  - Text/jSON
  - Application/PhP
  - Script/PhP
  - Application/Javascript
- Ensure the file has Write Permissions. (LINUX users)

#### Security
The Script must be only Requisited by Habbo Emulators. (Following the Habbo Camera API) For that, you Need Have
an Adobe Flash Crosddomain Policy (X-CORS) file "crossdomain.xml". Without that any prejudicial user can execute the script.
[Read More Here](http://www.adobe.com/devnet/articles/crossdomain_policy_file_spec.html)

#### Warning
> the Installation Procedure and Test Procedure only will work in localhost, or in a ip from the white-list Array.

#### Dudes? Issues?
**Post [Here](https://github.com/sant0ro/habbo-camera-server/issues)**
