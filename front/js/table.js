let dragSrcEl = null;

function dragStart(event) {
    dragSrcEl = event.target.closest('th');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/html', dragSrcEl.outerHTML);
}

function allowDrop(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
}

function drop(event) {
    event.preventDefault();
    const target = event.target.closest('th');
    if (dragSrcEl !== target) {
        dragSrcEl.parentNode.removeChild(dragSrcEl);
        const dropHTML = event.dataTransfer.getData('text/html');
        target.insertAdjacentHTML('beforebegin', dropHTML);

        // Reinitialize drag-and-drop event listeners for the new element
        const newHeader = target.previousSibling;
        addDragDropHandlers(newHeader);
    }
}

function addDragDropHandlers(element) {
    element.setAttribute('draggable', 'true');
    element.ondragstart = dragStart;
    element.ondragover = allowDrop;
    element.ondrop = drop;
}
function addRow() {
    const table = document.getElementById('itemTable');
    const headerCells = table.querySelectorAll('thead th');
    const newRow = table.insertRow();
    const rowCount = table.rows.length - 1; // Adjust for the header row

    headerCells.forEach((headerCell, index) => {
        const newCell = newRow.insertCell(index);

        if (index === 0) {
            // Row number cell
            const rowNumberInput = document.createElement('input');
            rowNumberInput.type = 'text';
            rowNumberInput.value = rowCount;
            // rowNumberInput.disabled = true; // Make row number read-only
            newCell.appendChild(rowNumberInput);
        } else if (index === headerCells.length - 1) {
            // Action column cell
            const deleteButton = document.createElement('button');
            deleteButton.classList.add('button');
            deleteButton.textContent = 'Delete';
            deleteButton.onclick = function () { deleteRow(this); };
            newCell.appendChild(deleteButton);
        } else {
            // Data input cell
            const inputField = document.createElement('input');
            inputField.type = 'text';
            inputField.name = `rows[${rowCount}][]`;
            inputField.placeholder = `Enter ${headerCell.querySelector('input').value}`;
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
        addDragDropHandlers(newHeaderCell);

        const inputField = document.createElement('input');
        inputField.type = 'text';
        inputField.name = 'headers[]';
        inputField.placeholder = newColumnName;
        inputField.value = newColumnName;

        newHeaderCell.appendChild(inputField);

        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = function () { deleteColumn(newHeaderCell.cellIndex); };
        newHeaderCell.appendChild(deleteButton);

        headerRow.insertBefore(newHeaderCell, headerRow.lastElementChild);

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row) => {
            const newCell = row.insertCell(row.cells.length - 1);
            const inputField = document.createElement('input');
            inputField.type = 'text';
            inputField.name = `rows[${row.rowIndex}][]`;
            inputField.placeholder = `Enter ${newColumnName}`;
            newCell.appendChild(inputField);
        });
    }
}

function deleteRow(button) {
    const row = button.closest('tr');
    row.parentNode.removeChild(row);

}

function deleteColumn(columnIndex) {
    const table = document.getElementById('itemTable');
    const rows = table.rows;

    for (let i = 0; i < rows.length; i++) {
        rows[i].deleteCell(columnIndex);
    }
}

function saveTable() {
    const tableData = getTableData();
    localStorage.setItem('tableData', JSON.stringify(tableData));
    alert('Table data saved locally!');
}

function submitTable() {
    const tableData = getTableData();
    console.log('Submitting table data:', tableData);
    alert('Table data submitted!');
}

function getTableData() {
    const table = document.getElementById('itemTable');
    const headers = Array.from(table.querySelectorAll('thead th input')).map(input => input.value);
    const rows = Array.from(table.querySelectorAll('tbody tr')).map(row =>
        Array.from(row.querySelectorAll('td input')).map(input => input.value)
    );
    return { headers, rows };
}


// Initialize buttons and table on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {

    createButtonsAndDisplayData();
    displayExampleTable();
});

// Function to create buttons and display table data
function createButtonsAndDisplayData() {
    const buttonContainer = document.getElementById('buttonContainerpropsal');

    // Clear existing buttons
    buttonContainer.innerHTML = '';

    // Iterate over window.data and create buttons
    if (window.data && window.data.length > 0) {
        window.data.forEach((item, index) => {
            const button = document.createElement('button');
            button.textContent = `View Proposal ${index + 1}`;
            button.classList.add('btn', 'btn-primary', 'm-2');

            // Add click event listener to display table data
            button.addEventListener('click', (event) => {
                event.preventDefault();
                displayTableData(JSON.parse(item.data));
            });

            // Append button to the container
            buttonContainer.appendChild(button);
        });
    } else {
        // No data available, display a sample table
        displayExampleTable();
    }
}

// Function to display table data
function displayTableData(dataItem) {
    const table = document.getElementById('itemTable');
    const headerRow = table.querySelector('thead tr');
    const tbody = table.querySelector('tbody');

    // Clear existing table content
    headerRow.innerHTML = '';
    tbody.innerHTML = '';

    // Populate headers
    dataItem.head.forEach(header => {
        const th = document.createElement('th');
        addDragDropHandlers(th);

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'headers[]';
        input.placeholder = header;
        input.value = header;

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.classList.add('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = () => deleteColumn(th.cellIndex);

        th.appendChild(input);
        th.appendChild(deleteButton);
        headerRow.appendChild(th);
    });

    // Convert dataItem.body to an array if it's not already
    const bodyArray = Array.isArray(dataItem.body) ? dataItem.body : Object.values(dataItem.body);

    // Determine the number of columns from the headers
    const columnCount = dataItem.head.length;

    // Populate rows
    bodyArray.forEach((row, rowIndex) => {
        const tr = document.createElement('tr');

        // Ensure each row has the correct number of cells
        for (let cellIndex = 0; cellIndex < columnCount - 1; cellIndex++) {
            const td = document.createElement('td');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `rows[${rowIndex}][]`;
            input.placeholder = 'Enter value';
            input.value = row[cellIndex] || '';  // Use existing value or empty string
            td.appendChild(input);
            tr.appendChild(td);
        }

        // Add delete button at the end of the row
        const deleteTd = document.createElement('td');
        const deleteButton = document.createElement('button');
        deleteButton.classList.add('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = function () {
            tr.remove();

        };
        deleteTd.appendChild(deleteButton);
        tr.appendChild(deleteTd);

        tbody.appendChild(tr);
    });

    // Adjust rows if any rows are missing (empty rows)
    const rowCount = bodyArray.length;
    for (let i = rowCount; i < rowCount; i++) {
        const tr = document.createElement('tr');

        for (let cellIndex = 0; cellIndex < columnCount; cellIndex++) {
            const td = document.createElement('td');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `rows[${i}][]`;
            input.placeholder = 'Enter value';
            td.appendChild(input);
            tr.appendChild(td);
        }

        // Add delete button at the end of the empty row
        const deleteTd = document.createElement('td');
        const deleteButton = document.createElement('button');
        deleteButton.classList.add('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = function () {
            tr.remove();

        };
        deleteTd.appendChild(deleteButton);
        tr.appendChild(deleteTd);

        tbody.appendChild(tr);
    }
}

// Function to display an example table if no data exists
function displayExampleTable() {
    const table = document.getElementById('itemTable');
    const headerRow = table.querySelector('thead tr');
    const tbody = table.querySelector('tbody');

    // Example headers and data
    const exampleHeaders = ['Row-Number', 'Item-code', 'Name', 'Description', "action"];
    const exampleRows = [
        ['1', 'A123', 'Example Item 1', 'This is an example description 1'],
        ['2', 'B456', 'Example Item 2', 'This is an example description 2'],
        ['3', 'C789', 'Example Item 3', 'This is an example description 3']
    ];

    // Populate headers
    headerRow.innerHTML = '';
    exampleHeaders.forEach(header => {
        const th = document.createElement('th');
        addDragDropHandlers(th);

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'headers[]';
        input.placeholder = header;
        input.value = header;

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.classList.add('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = () => deleteColumn(th.cellIndex);

        th.appendChild(input);
        th.appendChild(deleteButton);
        headerRow.appendChild(th);
    });

    // Populate rows
    exampleRows.forEach((row, rowIndex) => {
        const tr = document.createElement('tr');
        row.forEach(cellData => {
            const td = document.createElement('td');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `rows[${rowIndex}][]`;
            input.placeholder = 'Enter value';
            input.value = cellData;
            td.appendChild(input);
            tr.appendChild(td);
        });

        // Optional: Add delete button for each row
        const deleteTd = document.createElement('td');
        const deleteButton = document.createElement('button');
        deleteButton.classList.add('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = function () {
            tr.remove();

        };
        deleteTd.appendChild(deleteButton);
        tr.appendChild(deleteTd);

        tbody.appendChild(tr);
    });
}
