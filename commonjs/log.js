//log that works without any dependencies (eg. in Selenium)

var debugDiv;

module.exports = function(msg) {
    if (!debugDiv) {
        debugDiv = document.createElement('div');
        document.body.appendChild(debugDiv);
        debugDiv.style.position = 'absolute';
        debugDiv.style.zIndex = '300';
        debugDiv.style.top = 0;
        debugDiv.style.right = 0;
        debugDiv.style.backgroundColor = 'white';
        debugDiv.style.fontSize = '10px';
    }
    debugDiv.innerHTML += msg+'<br />';
};
