# Php multithreading demo

The app spins up multiple parallel socket servers to which you can connect through telnet.  
Usage:  
`php src/demo.php <hostname> <port> [...port]`    

Example: run 3 servers on localhost:  
`php src/demo.php localhost 9001 9002 9003`
