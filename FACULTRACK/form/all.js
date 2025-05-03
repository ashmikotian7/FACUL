document.addEventListener("DOMContentLoaded", function () {
    const facultyForm = document.getElementById("faculty-details");
    const resultDiv = document.getElementById("result");

    // File preview
    document.querySelectorAll(".file-input").forEach(input => {
        input.addEventListener("change", function (event) {
            const preview = this.nextElementSibling;
            preview.innerHTML = "";
            const file = event.target.files[0];

            if (file) {
                const fileName = document.createElement("p");
                fileName.textContent = `Uploaded file: ${file.name}`;
                preview.appendChild(fileName);

                const fileURL = URL.createObjectURL(file);
                if (file.type.startsWith("image/")) {
                    const img = document.createElement("img");
                    img.src = fileURL;
                    img.style.maxWidth = "200px";
                    preview.appendChild(img);
                } else if (file.type === "application/pdf") {
                    const iframe = document.createElement("iframe");
                    iframe.src = fileURL;
                    iframe.width = "100%";
                    iframe.height = "200px";
                    preview.appendChild(iframe);
                } else {
                    const link = document.createElement("a");
                    link.href = fileURL;
                    link.target = "_blank";
                    link.textContent = "Click to view uploaded file";
                    preview.appendChild(link);
                }
            }
        });
    });

    // Grade Point Calculation
    function calculateTotal() {
        function calculateSection(tableId, totalId) {
            const table = document.querySelector(tableId);
            const rows = table.querySelectorAll("tbody tr");
            let total = 0;

            rows.forEach(row => {
                const input = row.querySelector(".grading-input");
                const cell = row.querySelector(".grade-point");

                if (input && cell) {
                    const grade = parseFloat(input.value) || 0;
                    const weight = parseFloat(input.dataset.weight) || 0;
                    const point = grade * weight;
                    cell.textContent = point.toFixed(2);
                    total += point;
                }
            });

            document.getElementById(totalId).textContent = total.toFixed(2);
            return total;
        }

        const total1 = calculateSection("#evaluation-table", "total-grade-point1");
        const total2 = calculateSection("#research-table", "total-grade-point2");
        const total3 = calculateSection("#hod-table", "total-grade-point3");

        const grand = total1 + total2 + total3;
        document.getElementById("grand-total").textContent = grand.toFixed(2);
    }

    document.querySelectorAll(".grading-input").forEach(input => {
        input.addEventListener("input", calculateTotal);
    });

    window.addEventListener("load", calculateTotal);

    // PDF & Submission
    document.getElementById("submit-form").addEventListener("click", function () {
        const button = this;
        const content = document.querySelector(".container");
        if (!content) {
            alert("Could not find content to export.");
            return;
        }

        button.disabled = true;

        const academics = parseInt(document.getElementById("academics").value) || 0;
        const research = parseInt(document.getElementById("research").value) || 0;
        const admin = parseInt(document.getElementById("administrative").value) || 0;
        const hod = parseInt(document.getElementById("hod").value) || 0;
        const driveLink = document.getElementById("drive-link").value || "No link provided";
        const year = parseInt(document.getElementById("year").value) || 0;

        const total = academics + research + admin + hod;
        let allowance = "No allowance";
        let grade = "A";

        if (total >= 200) {
            allowance = "₹5,000";
            grade = "A+++";
        } else if (total >= 160) {
            allowance = "₹3,500";
            grade = "A++";
        } else if (total >= 120) {
            allowance = "₹2,000";
            grade = "A+";
        }

        const summaryHTML = `
            <div id="pdf-summary" style="margin-top:20px">
                <hr>
                <h3><u>Evaluation Summary</u></h3>
                <p><strong>Total Score:</strong> ${total}</p>
                <p><strong>Grade:</strong> ${grade}</p>
                <p><strong>Performance Allowance:</strong> ${allowance}</p>
                <p><strong>Drive Link:</strong> ${driveLink}</p>
                <p><strong>Year:</strong> ${year}</p>
            </div>
        `;
        resultDiv.innerHTML = summaryHTML;

        const formData = new FormData();
        formData.append("totalScore", total);
        formData.append("grade", grade);
        formData.append("allowance", allowance);
        formData.append("driveLink", driveLink);
        formData.append("year", year);

        fetch("store_database.php", {
            method: "POST",
            body: formData
        }).then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert("Error storing data: " + data.message);
                button.disabled = false;
                return;
            }

            // Wait for DOM update
            setTimeout(() => {
                const options = {
                    margin: 10,
                    filename: "Performance_Evaluation_Form.pdf",
                    image: { type: "jpeg", quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: "mm", format: "a4", orientation: "portrait" }
                };

                html2pdf().set(options).from(content).save()
                .then(() => {
                    alert("PDF downloaded!");
                    facultyForm.reset();
                    resultDiv.innerHTML = "";
                    calculateTotal();
                    button.disabled = false;
                })
                .catch(err => {
                    console.error("PDF error:", err);
                    alert("Failed to generate PDF.");
                    button.disabled = false;
                });
            }, 300); // Ensure summary is visible
        })
        .catch(err => {
            console.error("Store error:", err);
            alert("Failed to store data.");
            button.disabled = false;
        });
    });
});
