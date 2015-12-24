var link = "http://plus.local/profile/viewxml";
var xmldoc,xsldoc;


var xmlhttp=new XMLHttpRequest();
xmlhttp.onreadystatechange = function(){
    if(this.readyState == 4){
        if(this.status == 200){
            xmldoc = xmlhttp.responseXML;
        }
        else{ window.alert("Error "+ xmlhttp.statusText); }
    }
};
xmlhttp.open("GET",link,false);
xmlhttp.send(null);


var xslhttp=new XMLHttpRequest();
xslhttp.onreadystatechange = function(){
    if(this.readyState == 4){
        if(this.status == 200){
            xsldoc = xslhttp.responseXML;
        }
        else{ window.alert("Error "+ xslhttp.statusText); }
    }
};
xslhttp.open("GET","/js/viewProcessor.xml",false);
xslhttp.send(null);

if (document.implementation && document.implementation.createDocument) {
    xsltProcessor = new XSLTProcessor();
    xsltProcessor.importStylesheet(xsldoc);
    resultDocument = xsltProcessor.transformToFragment(xmldoc, document);
    document.getElementById("xslt").appendChild(resultDocument);
}