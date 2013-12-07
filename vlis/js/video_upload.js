    var file, p=true, index=0, dropZone, upload_btn;
    window.onload = function(){
        dropZone = document.getElementById("uploadfile");
        dropZone.addEventListener("dragenter", dragenter, false);
        dropZone.addEventListener("dragover", dragover, false);
        dropZone.addEventListener("drop", function(evt){
            evt.stopPropagation();
            evt.preventDefault();
            evt.dataTransfer.dropEffect = "copy";
            
            file = evt.dataTransfer.files[0];        
          }, false);
        dropZone.addEventListener("click", getFile, false);
        document.getElementById("video_file").addEventListener("change", function(evt){
            file = evt.target.files[0];
            dropZone.innerHTML = 'File has been selected';
            dropZone.style.fontWeight = 'bold';
          }, false);
          
        document.getElementById("upload_video_btn").addEventListener("click", function(evt){
            upload_btn = this;
            upload_btn.style.visibility="hidden";
            startUpload();
          }, false);          
    }
                
    function dragenter(e) {
        e.stopPropagation();
        e.preventDefault();
    }
    
    function dragover(e){
        e.stopPropagation();
        e.preventDefault();
    }                
                
    function getFile(){
      document.getElementById("video_file").click();
    }   
    
    function startUpload(evt){
        var form, cat_id, title, presenter, desc, tag, div, error_p, regex, post_data, filename, post_result;
        form = document.forms.video_upload_form;
        cat_id = form.video_categories.value.trim();
        title = form.video_title.value.trim();
        presenter = form.video_info.value.trim();
        desc = form.video_desc.value.trim();
        tag = form.video_tag.value.trim();
        div = document.getElementById("upload_video");
        regex = /^video\/*/;
        
        if (document.getElementById("upload_error"))
          error_p = document.getElementById("upload_error");
        else{
          error_p = document.createElement("p");
          error_p.setAttribute("id", "upload_error");
        }
        
        if (cat_id == ""){        
          error_p.innerHTML = "<p>Category must be selected</p>";
          div.insertBefore(error_p, div.firstChild);
          upload_btn.style.visibility="visible";
          return;
        }else if (title == ""){
            error_p.innerHTML = "<p>Title is Required</p>";
            div.insertBefore(error_p, div.firstChild);
            upload_btn.style.visibility="visible";
            return;            
        }else if (!file){
            error_p.innerHTML = "<p>A video file is needed</p>";
            div.insertBefore(error_p, div.firstChild);
            upload_btn.style.visibility="visible";
            return;            
        }else if (!regex.test(file.type)){
            error_p.innerHTML = "<p>Only video files accepted</p>";
            div.insertBefore(error_p, div.firstChild);
            upload_btn.style.visibility="visible";
            return;            
        }else{            
            if (file.name.indexOf("/") > 0) //linux-based path
              filename = file.name.split("/")[-1];
            else if (file.name.indexOf("\\") > 0) //windows-based path
              filename = file.name.split("\\")[-1];
            else //no path at all
              filename = file.name;  
            
            post_data = "video_categories=" + cat_id + "&video_title=" + title +
                "&video_info=" + presenter + "&video_desc=" + desc +
                "&video_tag=" + tag + "&video_name=" + filename + "&video_type=" + file.type +
                "&upload_video_btn=y";
            post_result = sendDataFirst(post_data);
            
            if (post_result.code=="0"){
                error_p.removeAttribute("id");
                error_p.setAttribute("id", "upload_success");
                error_p.innerHTML = "<p>" + post_result.message + "</p>";
                div.insertBefore(error_p, div.firstChild);
                
                process(); //start file upload
                
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "/upload", false);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
                xhr.onload = function(e){
                    if (xhr.readyState == 4 && xhr.status==200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.code=="0"){
                            error_p.innerHTML = response.message;
                            window.setTimeout(function(){location.reload(true);} , 5000);
                        }else
                            error_p.innerHTML = response.message; 
                    }
                }
                xhr.send("uploaded_complete=done");
        
            }else{
                error_p.innerHTML = "<p>" + post_result.message + "</p>";
                div.insertBefore(error_p, div.firstChild);
            }
        }  
    }                
              
    function makeActive(element, id_of_content){
       if (!$(element).hasClass("active")){
           $("div#upload_videos_header h2").removeClass("active");
           $("#upload_video, #manage_uploaded_video").css("display", "none");
           $(element).addClass("active");
           $("#" + id_of_content).css("display", "block");
       }
    }
                
    function process(){
        var blob = file;
          
        const BYTES_PER_CHUNK = 1024 * 1024;
        const SIZE = blob.size;
        
        var start = 0;
        var end = BYTES_PER_CHUNK;
        
        while(start < SIZE){
            var chunk = blob.slice(start, end);
            upload(chunk);
            
            start = end;
            end = start + BYTES_PER_CHUNK;
             ++index;
        }
    }                
                
    function sendDataFirst(data){
        var response;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/upload", false);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function(e){
            if (xhr.readyState == 4 && xhr.status==200)
                response = JSON.parse(xhr.responseText);
        }
        xhr.send(data);
        return response;
    }
                

    function upload(blobOrFile){
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/upload/" + index, false);
        xhr.onload = function(e){
              if (xhr.readyState == 4 && xhr.status == 200) {
                file_response = xhr.responseText;
              }
            };
        xhr.send(blobOrFile);
    }