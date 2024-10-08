// Function to format numbers with commas
function formatNumberWithCommas(number) {
    if (typeof number !== 'string') {
        number = number.toString(); // Convert to string if it's not already
    }
    return Number(number.replace(/,/g, '')).toLocaleString('en-US');
}

// Assume that window.ibx is already populated
const ibx = (buget && Array.isArray(buget) && buget[0] && typeof buget[0].data === 'string')
    ? JSON.parse(buget[0].data)
    : buget;

if (ibx && ibx.body && ibx.body.length > 0) {
    const bugetIndex = ibx.head.indexOf('buget');

    ibx.body.forEach(row => {
        row.forEach((value, index) => {
            const amounts = value.split(':').map(val => parseFloat(val.replace(/,/g, '')) || val.trim()) || value;
            const [change, total] = amounts;


            if (total === change) {
                // If total equals change, show only the total value
                row[index] = `${total}`;
            } else if (index === bugetIndex && total) { // Only process the Budget column
                if (total > change) {  // If total is greater than change (indicating a decrease)
                    const percentageDifference = ((total - change) / total) * 100;
                    row[index] = `${formatNumberWithCommas(change)} <span style="color: red">↓</span> ${percentageDifference.toFixed(2)}%`;
                } else {  // If total is less than change (indicating an increase)
                    const percentageDifference = ((change - total) / total) * 100;
                    row[index] = `${formatNumberWithCommas(change)} <span style="color: green">↑</span> ${percentageDifference.toFixed(2)}%`;
                }
            }
        });
    });
} else {
    notyf.open({
        type: 'error',
        message: "No budget set in your database"
    });
}

// Generate the table
function generateTable() {
    if (!ibx || !ibx.body || ibx.body.length === 0) {
        console.error("No data available to generate the table.");
        return;
    }

    let table = '<table border="1">';

    // Header row
    table += '<tr>';
    ibx.head.forEach(header => {
        table += `<th>${header}</th>`;
    });
    table += '</tr>';

    // Body rows
    ibx.body.forEach(row => {
        table += '<tr>';
        row.forEach(value => {
            table += `<td>${value}</td>`;
        });
        table += '</tr>';
    });

    table += '</table>';

    // Display the table
    const tableContainer = document.getElementById('table-container');
    if (tableContainer) {
        tableContainer.innerHTML = table;
    } else {
        console.error("Element with id 'table-container' not found.");
    }
}

// Call the function to generate the table
generateTable();
