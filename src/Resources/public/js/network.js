var network = null;

function draw() {
    console.log('networkData', networkData);
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
        layout: {
            randomSeed: 314919,
        },
        interaction: {
            dragNodes:true,
            dragView: true,
            selectable: true,
            selectConnectedEdges: true,
            zoomView: true
        },
        manipulation: {
            editEdge: false,
            addNode: false,
            addEdge: false,
            deleteNode: false,
            deleteEdge: false
        },
        physics: {
            stabilization: {
                enabled: true
            },
            barnesHut: {
                gravitationalConstant: -23000,
                centralGravity: 0,
                springLength: 0,
                springConstant: 0.5,
                damping: 1,
                avoidOverlap: 1
            }
        }
    };
    network = new vis.Network(container, networkData, options);
    console.log(network.getSeed());
}

window.addEventListener("load", () => {
    draw();
});
