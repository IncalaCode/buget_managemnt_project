function addRow() {
    const table = document.getElementById('itemTable');
    const headerCells = table.querySelectorAll('thead th'); // Get the number of columns from the header
    const newRow = table.insertRow();
    const rowCount = table.rows.length;

    headerCells.forEach((headerCell, index) => {
        const newCell = newRow.insertCell(index);

        if (index === 0) {
            newCell.textContent = rowCount - 1; // Set the row number
        } else {
            const inputField = document.createElement('input');
            inputField.type = 'text';
            inputField.placeholder = `Enter ${headerCell.textContent}`;
            newCell.appendChild(inputField);
        }
    });
}

function addColumn() {
    const table = document.getElementById('itemTable');
    const headerRow = table.querySelector('thead tr');
    const newColumnName = prompt('Enter column name:');
    
    if (newColumnName) {
        const newHeaderCell = document.createElement('th');
        newHeaderCell.textContent = newColumnName;
        headerRow.appendChild(newHeaderCell);

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row) => {
            const newCell = row.insertCell();
            const inputField = document.createElement('input');
            inputField.type = 'text';
            inputField.placeholder = `Enter ${newColumnName}`;
            newCell.appendChild(inputField);
        });
    }
}

function saveTable() {
    const tableData = getTableData();
    localStorage.setItem('tableData', JSON.stringify(tableData));
    alert('Table data saved locally!');
}

function submitTable() {
    const tableData = getTableData();
    // Code to submit tableData to the server or process it as needed
    console.log('Submitting table data:', tableData);
    alert('Table data submitted!');
}

function getTableData() {
    const table = document.getElementById('itemTable');
    const rows = table.querySelectorAll('tbody tr');
    const data = [];

    rows.forEach((row) => {
        const rowData = [];
        row.querySelectorAll('td').forEach((cell, index) => {
            if (index === 0) {
                rowData.push(cell.textContent); // Row-Number
            } else {
                const inputField = cell.querySelector('input');
                rowData.push(inputField ? inputField.value : '');
            }
        });
        data.push(rowData);
    });

    return data;
}
