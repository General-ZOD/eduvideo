var file=[], p =true, index=0;
function upload(blobOrFile){
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/uploaded.php?index=" + index, false);
    xhr.onload = function(e){};
    xhr.send(blobOrFile);
}

function process(){
    for (var j=0; j < file.length; j++){
      var blob = file[j];
      
    const BYTES_PER_CHUNK = 1024 * 1024;
    const SIZE = blob.size;
    
    var start = 0;
    var end = BYTES_PER_CHUNK;
    
    while(start < SIZE){
        var chunk = ("mozSlice" in blob)? blob.mozSlice(start, end): blob.webkitSlice(slice, end);
        upload(chunk);
        
        start = end;
        end = start + BYTES_PER_CHUNK;
    }
    p = (j = file.length - 1)? true: false;
    self.postMessage(blob.name + " Uploaded successfully");
    ++index;
  }
}

self.onmessage = function(e){
    for (var j=0; j < e.data.length; j++)
        file.push(e.data[j]);
    
    if (p)
      process();
}