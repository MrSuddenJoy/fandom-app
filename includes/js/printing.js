function setPrint() {
    const closeOnPrintDone = () => {
        document.removeChild(this);
    };
    this.contentWindow.onafterprint = closeOnPrintDone;
    this.contentWindow.print();
}

document.getElementById("print_external").addEventListener("click", () => {
    const hideFrame = document.createElement("iframe");
    hideFrame.onload = setPrint;
    hideFrame.style.display = "none"; // hide iframe
    hideFrame.src = "external-page.html";
    document.body.appendChild(hideFrame);
  });