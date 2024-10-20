var network = null;

function draw() {
    var container = document.getElementById("mynetwork");
    var options = {
        edges: {
            arrows: {
                to: {
                    enabled: true
                },
            },
        },
        nodes: {
            size: 30,
            shapeProperties: {
                useImageSize: true,
                useBorderWithImage: false,
                interpolation: false
            },
        },
    };
    network = new vis.Network(container, networkData, options);
}

window.addEventListener("load", () => {
    draw();
});
