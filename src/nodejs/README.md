# Fast RPC

To translate Mapy.cz ID (Place ID) or PID (Panorama ID) to coordinates, needs to do request to Mapy.cz API which is accepting only FRPC (FastRPC) 
formatted request data. Implementation is currently available only in Javascript 
[here](https://github.com/seznam/JAK/blob/master/util/frpc.js) and until rewritten to PHP needs to be done by running 
simple dummy NodeJS server, which can read and evaluate Javascript code.

## Workflow
### Place ID
Really dumb and simple: 
1. PHP match "id=???" and "source=???" in Mapy.cz link
1. PHP execute request to this dummy NodeJS server with these two parameters
1. NodeJS generate payload, simulate frontend request to MapyCZ API
1. MapyCZ API returns data in JSON (default was FRPC but it can be changed to JSON or XML)
1. NodeJS return MapyCZ response back to PHP
### Panorama ID
Very similar to Place ID but little bit complicated: 
1. PHP match "pid=???" in Mapy.cz link
1. PHP execute request to this dummy NodeJS server with this parameter
1. NodeJS generate payload, simulate frontend request to MapyCZ API which returns list of neighbours for this PID (sadly, not coordinates for itself)
1. NodeJS will choose one of these neighbours
1. NodeJS will generate another payload and simulate another frontend request for this neigbour which return list of neighbours and one of
 them should* has original PID (so now we have coordinates for original PID)    
1. MapyCZ API returns data in JSON (default was FRPC but it can be changed to JSON or XML)
1. NodeJS return MapyCZ response back to PHP

*In rare case that there is no original neighbour, it will return coordinates one of neighbours from first request.

## Requirements
- NodeJS (tested on v12 but it should work on lower versions too)

## Installation
Update `MAPY_CZ_DUMMY_SERVER` in your local PHP config to URL of this NodeJS server.

Default is `null`: instead of translating ID will be used fallback to inaccurate X and Y coordinates from URL which
are position of map, not selected point.  

## Running
`node fastrpc.js`

## More info

- Issues to this topic: 
[#1](https://github.com/DJTommek/better-location/issues/1),
[#4](https://github.com/DJTommek/better-location/issues/4)
- Pulls to this topic: [#3](https://github.com/DJTommek/better-location/pull/3)
- Commits to this topic: [1d41c0a](https://github.com/DJTommek/better-location/commit/1d41c0a72b8fe6ac23f792a7f3290ab4c4562c87)
- Mapy.cz API can change any time so I recommend to test it in browser before creating NodeJS server instance 
by simply opening [FastRPC.html](../../fastrpc.html) and inserting some your own mapy.cz link.
- MapyCZ API is probably accepting XML too: for empty payload with `Content-Type: text/xml` response was:<br> 
    ```
    HTTP 200 OK
    {"failure": -503, "failureMessage": "Parser error: < XML_ERR_DOCUMENT_END >"}
    ```
    Tried generate some XML payloads but without luck, it keeps responding error above or HTTP 400 Bad Request.
        
- As writing this text on 2020-07-23 I'm hoping, that these libraries will be rewritten to PHP *soon* and this
dummy NodeJS server doesn't deserve it's own repository.
- I know that this is overkill but for me it's much easier and quicker to write simple NodeJS than understanding JAK 
code and transcripting it to PHP. Feel free to help! :)