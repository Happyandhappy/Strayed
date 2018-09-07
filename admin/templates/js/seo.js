//Autor dejan@zdravkovic.rs
var a = {"а":"a","б":"b","в":"v","г":"g","д":"d","ђ":"dj","е":"e","ж":"z","з":"z","и":"i","ј":"j","к":"k","л":"l","љ":"lj","м":"m","н":"n","њ":"nj","о":"o","п":"p","р":"r","с":"s","т":"t","ћ":"c","у":"u","ф":"f","х":"h","ц":"c","ч":"c","џ":"dz","ш":"s","А":"A","Б":"B","В":"V","Г":"G","Д":"D","Ђ":"Dj","Е":"E","Ж":"Z","З":"Z","И":"I","Ј":"J","К":"K","Л":"L","Љ":"Lj","М":"M","Н":"N","Њ":"Nj","О":"O","П":"P","Р":"R","С":"S","Т":"T","Ћ":"C","У":"U","Ф":"F","Х":"H","Ц":"C","Ч":"C","Џ":"Dz","Ш":"S"};

function trans(rec){
  return rec.split('').map(function (char) { 
    return a[char] || char; 
  }).join("");
};

function seo(txt_src){
 txt_src=trans(txt_src);
 var output = txt_src.replace(/[^a-zA-Z0-9]/g,' ').replace(/\s+/g,"-").toLowerCase();
 /* remove first dash */
 if(output.charAt(0) == '-') output = output.substring(1);
 /* remove last dash */
 var last = output.length-1;
 if(output.charAt(last) == '-') output = output.substring(0, last);
 
 //return output + "-" + Math.round(Math.random()*100)+".html";
 return output;
};