<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="https://cloudflare.com">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/style.css'); ?>">
<div>
    <h2 class="text-2xl font-bold mb-6">Products List</h2>

    <table id="productsTable" border="1">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Name</th>
            </tr>
        </thead>
         <tbody>
            <?php if(!empty($products)): ?>
                <?php foreach($products as $row): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['product_name']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<h1>Ordert Placing:</h1>
<div class="table-container">
    <h2>Order Management Table</h2>
    <div class="table-actions" style="margin-bottom: 15px;">
        <button type="button" class="btn btn-group" onclick="groupAndSaveRows()" style="background-color: #007bff; color: white; padding: 10px 15px;">
            Group
        </button>
         
    </div>
    <table id="orderTable">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>Product Name</th>
                <th>T.Cards</th>
                <th>Size</th>
                <th>Piece</th>
                <th>Weight</th>
                <th>Melting</th>
                <th>Touch</th>
                <th>Pure</th>
                <th>Rate</th>
                <th><button type="button" class="btn btn-group" onclick="addNewRow()" style="background-color: green; color: white; padding: 10px 15px;">
            Add New
        </button></th>
            </tr>
        </thead>
        <tbody>
            <tr class="order-row" data-row-index="0">
               <td style="text-align: center; vertical-align: middle;">
                    <input type="checkbox" class="row-select" value="">
                    <div class="group-actions-wrapper" style="display: none; flex-direction: column; gap: 5px; align-items: center;">
                        <button type="button" class="btn btn-edit-group" onclick="enableGroupEditing(this)" title="Edit Group" style="background-color: #ffc107; color: #212529; padding: 6px 10px;">
                            Edit
                        </button>
                        <button type="button" class="btn btn-save-group" onclick="saveGroupEdits(this)" title="Save Changes" style="background-color: #17a2b8; color: white; padding: 6px 10px; display: none;">
                            Update
                        </button>
                    </div>
                </td>
                <td>
                    <div class="dropdown-container">
                        <input type="text" class="search-select product-name-input" placeholder="Select Product..." onclick="toggleDropdown(this)" onkeyup="filterProducts(this)">
                        <div class="dropdown-options">
                            <?php foreach($products as $row): ?>
                                <div onclick="selectProduct(this, '<?=$row['product_name']?>', '<?=$row['id']?>')"><?=$row['product_name']?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </td>
                <td><input type="text" class="t-cards-input" oninput="updateGrandTotals()"></td>
                <td><input type="number" class="size-input" oninput="calculateRow(this)"></td>
                <td><input type="number" class="piece-input" oninput="calculateRow(this)"></td>
                <td><input type="number" class="weight-input" readonly></td>
                <td><input type="number" class="melting-input" oninput="calculateRow(this)"></td>
                <td><input type="number" class="touch-input" oninput="calculateRow(this);updateGrandTotals();"></td>
                <td><input type="number" class="pure-input" readonly></td>
                <td><input type="number" class="rate-input" readonly></td>
                <input type="hidden" class="group-id-input" value="">
                <td  style="width:15%;"> 
                    <button type="button" class="btn btn-clone" onclick="cloneRow(this)" title="Clone Row">&#10064;</button>
                    <button type="button" class="btn btn-delete" onclick="deleteRow(this)">-</button>
                </td>
            </tr>           
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f3f5; font-weight: bold;">
                <td colspan="9" style="text-align: right; padding: 10px 15px; border-right: none;">Total Cards:</td>
                <td colspan="2" style="border-left: none;">
                    <input type="text" id="grandTotalCards" readonly style="font-weight: bold; background-color: #e2e8f0; width: 90%;">
                </td>
            </tr>
            <tr style="background-color: #f1f3f5; font-weight: bold;">
                <td colspan="9" style="text-align: right; padding: 10px 15px; border-right: none;">Total Weight:</td>
                <td colspan="2" style="border-left: none;">
                    <input type="text" id="grandTotalWeight" readonly style="font-weight: bold; background-color: #e2e8f0; width: 90%;">
                </td>
            </tr>
            <tr style="background-color: #f1f3f5; font-weight: bold;">
                <td colspan="9" style="text-align: right; padding: 10px 15px; border-right: none;">Total Pure:</td>
                <td colspan="2" style="border-left: none;">
                    <input type="text" id="grandTotalPure" readonly style="font-weight: bold; background-color: #e2e8f0; width: 90%;">
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<script>
    let globalRowCounter = 1;
    
async function groupAndSaveRows() {
    const checkboxes = document.querySelectorAll('.row-select:checked');
    if (checkboxes.length < 2) {
        alert("Please select at least 2 rows to group and save.");
        return;
    }

    for (let cb of checkboxes) {
        if (!cb.value) {
            alert("One or more selected rows do not have a valid product chosen.");
            return;
        }
    }

    const uniqueGroupId = 'GRP-' + Math.floor(Math.random() * 16777215).toString(16).toUpperCase().padStart(6, '0');
    const rowsToGroup = [];
    const payloadPackage = []; 

    let groupSumCards = 0;
    let groupSumSize = 0;
    let groupSumPieces = 0;
    let groupSumWeight = 0;
    let groupSumMelting = 0;
    let groupSumTouch = 0;
    let groupSumPure=0;

    checkboxes.forEach(cb => {
        const row = cb.closest('tr');
        rowsToGroup.push(row);

        const t_cards = parseFloat(row.querySelector('.t-cards-input').value) || 0;
        const size = parseFloat(row.querySelector('.size-input').value) || 0;
        const piece = parseFloat(row.querySelector('.piece-input').value) || 0;
        const weight = parseFloat(row.querySelector('.weight-input').value) || 0;
        const melting = parseFloat(row.querySelector('.melting-input').value) || 0;
        const touch = parseFloat(row.querySelector('.touch-input').value) || 0;
        const pure = parseFloat(row.querySelector('.pure-input').value) || 0;

        groupSumCards += t_cards;
        groupSumSize += size;
        groupSumPieces += piece;
        groupSumWeight += weight;
        groupSumMelting += melting;
        groupSumTouch += touch;
        groupSumPure +=pure

        payloadPackage.push({
            product_id: cb.value,
            product_name: row.querySelector('.product-name-input').value,
            t_cards: t_cards,
            size: size,
            piece: piece,
            weight: weight,
            melting: melting,
            touch: touch,
            pure: row.querySelector('.pure-input').value,
            rate: row.querySelector('.rate-input').value,
            group_id: uniqueGroupId 
        });
    });

    try {
        const response = await fetch('<?= base_url("orders/store") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ orders: payloadPackage })
        });

        const result = await response.json();
        if (!result.success) {
            alert("Database storage failure: " + result.message);
            return; 
        }
    } catch (error) {
        console.error("Transmission breakdown:", error);
        alert("Server communication error.");
        return; 
    }

    const baseRow = rowsToGroup[0];
    baseRow.classList.add('grouped-row-block');
    baseRow.setAttribute('data-assigned-group-id', uniqueGroupId);

    for (let colIndex = 1; colIndex <= 9; colIndex++) {
        const cellContainer = document.createElement('div');
        cellContainer.className = 'grouped-cell-container';

        rowsToGroup.forEach(row => {
            const structuralField = row.cells[colIndex].firstElementChild;
            if (structuralField) cellContainer.appendChild(structuralField);
            
            const groupField = row.querySelector('.group-id-input');
            if (groupField) groupField.value = uniqueGroupId;
        });

        let totalInputHTML = '';
        if (colIndex === 2) { 
            totalInputHTML = `\n<input type="text" class="t-cards-input total-sum-field" value="${groupSumCards}" readonly="">`;
        }  else if (colIndex === 4) { 
            totalInputHTML = `\n<input type="number" class="piece-input total-sum-field" value="${groupSumPieces}" readonly="">`;
        } else if (colIndex === 5) { 
            totalInputHTML = `\n<input type="number" class="weight-input total-sum-field" value="${groupSumWeight.toFixed(2)}" readonly="">`;
        } else if (colIndex === 6) { 
            totalInputHTML = `\n<input type="number" class="melting-input total-sum-field" value="${groupSumMelting}" disabled="true" style="background-color: transparent;" readonly="">`;
        } else if (colIndex === 7) { 
            totalInputHTML = `\n<input type="number" class="touch-input total-sum-field" value="${groupSumTouch}" disabled="true" style="background-color: transparent;" readonly="">`;
        }else if (colIndex === 8) { 
            totalInputHTML = `\n<input type="number" class="pure-input total-sum-field" value="${groupSumPure}" readonly="">`;
        }

        baseRow.cells[colIndex].innerHTML = '';
        baseRow.cells[colIndex].appendChild(cellContainer);
        
        if (totalInputHTML) {
            cellContainer.insertAdjacentHTML('beforeend', totalInputHTML);
        }
    }

    rowsToGroup.forEach((row, i) => {
        if (i > 0) {
            const groupField = row.querySelector('.group-id-input');
            if (groupField) baseRow.appendChild(groupField);
            row.remove(); 
        }
    });

    const mainCheckbox = baseRow.querySelector('.row-select');
    const actionsWrapper = baseRow.querySelector('.group-actions-wrapper');
    
    if (mainCheckbox) mainCheckbox.style.display = 'none';
    if (actionsWrapper) actionsWrapper.style.display = 'flex';

    const actionsCell = baseRow.cells[10]; 
    if (actionsCell) {
        actionsCell.style.display = 'none';
    }

    document.getElementById('selectAll').checked = false;
    alert("Products grouped under ID (" + uniqueGroupId + ") and stored successfully!");
}

function calculateRow(input) {
    const container = input.closest('.grouped-cell-container, tr');
    
    let sizeField = container.querySelector('.size-input') || input.closest('tr').querySelector('.size-input');
    let pieceField = container.querySelector('.piece-input') || input.closest('tr').querySelector('.piece-input');
    let meltingField = container.querySelector('.melting-input') || input.closest('tr').querySelector('.melting-input');
    let touchField = container.querySelector('.touch-input') || input.closest('tr').querySelector('.touch-input');

    if (input.closest('.grouped-cell-container')) {
        const parentCell = input.closest('td');
        const cellContainer = input.closest('.grouped-cell-container');
        const inputsInThisCell = Array.from(cellContainer.children);
        const processingIndex = inputsInThisCell.indexOf(input);

        const row = input.closest('tr');
        sizeField = row.querySelectorAll('.size-input')[processingIndex];
        pieceField = row.querySelectorAll('.piece-input')[processingIndex];
        meltingField = row.querySelectorAll('.melting-input')[processingIndex];
        touchField = row.querySelectorAll('.touch-input')[processingIndex];

        const size = parseFloat(sizeField.value) || 0;
        const piece = parseFloat(pieceField.value) || 0;
        const melting = parseFloat(meltingField.value) || 0;
        const touch = parseFloat(touchField.value) || 0;

        row.querySelectorAll('.weight-input')[processingIndex].value = (size * piece).toFixed(2);
        row.querySelectorAll('.pure-input')[processingIndex].value = (melting + touch).toFixed(2);
        row.querySelectorAll('.rate-input')[processingIndex].value = (melting + touch).toFixed(2);
    } else {
        const size = parseFloat(sizeField.value) || 0;
        const piece = parseFloat(pieceField.value) || 0;
        const melting = parseFloat(meltingField.value) || 0;
        const touch = parseFloat(touchField.value) || 0;

        container.querySelector('.weight-input').value = (size * piece).toFixed(2);
        container.querySelector('.pure-input').value = (melting + touch).toFixed(2);
        container.querySelector('.rate-input').value = (melting + touch).toFixed(2);
    }

    updateGrandTotals();
}

function cloneRow(button) {
    const row = button.closest('tr');
    const clone = row.cloneNode(true);
    
    clone.classList.remove('grouped-row-block');
    clone.setAttribute('data-row-index', globalRowCounter);

    for(let i = 1; i <= 9; i++) {
        const container = clone.cells[i].querySelector('.grouped-cell-container');
        if(container) {
            const item = container.firstElementChild;
            clone.cells[i].innerHTML = '';
            if(item) clone.cells[i].appendChild(item);
        }
    }

    clone.querySelectorAll('input').forEach(input => {
        if(input.type === 'checkbox') input.checked = false;
        //else if(!input.hasAttribute('readonly')) input.value = '';
    });

    clone.querySelectorAll('input[readonly]').forEach(i => i.value = '');
    const gId = clone.querySelector('.group-id-input');
    if(gId) gId.value = '';

    globalRowCounter++;
    row.parentNode.appendChild(clone);
}

function deleteRow(button) {
    const tbody = button.closest('tbody');
    if (tbody.querySelectorAll('tr').length > 1) {
        button.closest('tr').remove();
        updateGrandTotals();
    } else {
        alert("Cannot wipe final active table line row instance context.");
    }
}

function toggleDropdown(input) {
    document.querySelectorAll('.dropdown-options').forEach(d => d.style.display = 'none');
    input.nextElementSibling.style.display = 'block';
}
function filterProducts(input) {
    const filter = input.value.toUpperCase();
    const options = input.nextElementSibling.getElementsByTagName('div');
    for (let i = 0; i < options.length; i++) {
        let txt = options[i].textContent || options[i].innerText;
        options[i].style.display = txt.toUpperCase().indexOf(filter) > -1 ? "" : "none";
    }
}
function selectProduct(element, name, productId) {
    const row = element.closest('tr');
    
    row.querySelector('.search-select').value = name;
    
    const checkbox = row.querySelector('.row-select');
    if (checkbox) {
        checkbox.value = productId;
    }
    
    element.parentNode.style.display = 'none';
    updateGrandTotals();
}
document.addEventListener('click', e => {
    if (!e.target.matches('.search-select')) {
        document.querySelectorAll('.dropdown-options').forEach(d => d.style.display = 'none');
    }
});
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.row-select').forEach(cb => cb.checked = this.checked);
});
function addNewRow() {
    const tableBody = document.querySelector('#orderTable tbody');
    
    const baseRow = tableBody.querySelector('tr');
    if (!baseRow) {
        alert("System Error: No foundational row template found to construct a new line item.");
        return;
    }

    const newRow = baseRow.cloneNode(true);

    newRow.classList.remove('grouped-row-block');
    newRow.setAttribute('data-row-index', globalRowCounter);

    for (let i = 1; i <= 9; i++) {
        const nestedContainer = newRow.cells[i].querySelector('.grouped-cell-container');
        if (nestedContainer) {
            const individualItemField = nestedContainer.firstElementChild;
            newRow.cells[i].innerHTML = '';
            if (individualItemField) newRow.cells[i].appendChild(individualItemField);
        }
    }

    newRow.querySelectorAll('input').forEach(input => {
        if (input.type === 'checkbox') {
            input.checked = false;
            input.value = '';
        } else if (!input.hasAttribute('readonly')) {
            input.value = ''; 
        }
    });

    newRow.querySelectorAll('input[readonly]').forEach(input => input.value = '');
    
    const hiddenGroupField = newRow.querySelector('.group-id-input');
    if (hiddenGroupField) hiddenGroupField.value = '';

    
    newRow.querySelectorAll('input, select').forEach(element => {
        if (element.name) {
            element.name = element.name.replace(/orders\[\d+\]/, `orders[${globalRowCounter}]`);
        }
    });

    tableBody.appendChild(newRow);
    globalRowCounter++;
}

function updateGrandTotals() {
    let sumCards = 0;
    let sumWeight = 0;
    let sumPure = 0;

    document.querySelectorAll('.t-cards-input').forEach(input => {
        const val = input.value.trim();
        if (val !== "") {
            sumCards += (parseFloat(val) || 1); 
        }
    });

    document.querySelectorAll('.weight-input').forEach(input => {
        sumWeight += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('.pure-input').forEach(input => {
        sumPure += parseFloat(input.value) || 0;
    });

    document.getElementById('grandTotalCards').value = sumCards > 0 ? sumCards : 0;
    document.getElementById('grandTotalWeight').value = sumWeight.toFixed(2);
    document.getElementById('grandTotalPure').value = sumPure.toFixed(2);
}
async function saveGroupEdits(button) {
    const row = button.closest('tr');
    const wrapper = button.parentElement;
    const groupId = row.getAttribute('data-assigned-group-id');
    
    const payloadPackage = [];
    
    const productNames = row.querySelectorAll('.product-name-input');
    const tCards = row.querySelectorAll('.t-cards-input');
    const sizes = row.querySelectorAll('.size-input');
    const pieces = row.querySelectorAll('.piece-input');
    const weights = row.querySelectorAll('.weight-input');
    const meltings = row.querySelectorAll('.melting-input');
    const touches = row.querySelectorAll('.touch-input');
    const pures = row.querySelectorAll('.pure-input');
    const rates = row.querySelectorAll('.rate-input');

    for (let i = 0; i < productNames.length; i++) {
        payloadPackage.push({
            group_id: groupId,
            product_name: productNames[i].value,
            t_cards: tCards[i].value,
            size: sizes[i].value,
            piece: pieces[i].value,
            weight: weights[i].value,
            melting: meltings[i].value,
            touch: touches[i].value,
            pure: pures[i].value,
            rate: rates[i].value
        });
    }

    try {
        const response = await fetch('<?= base_url("group/update") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ group_id: groupId, grouped_products: payloadPackage })
        });

        const result = await response.json();
        if (!result.success) {
            alert("Failed to update database entries: " + result.message);
            return;
        }
    } catch (error) {
        console.error("Server update failure error trace:", error);
        alert("Server communication link fault.");
        return;
    }

    row.querySelectorAll('.grouped-cell-container input:not([readonly])').forEach(input => {
        input.setAttribute('disabled', 'true');
        input.style.backgroundColor = 'transparent';
    });

    button.style.display = 'none';
    wrapper.querySelector('.btn-edit-group').style.display = 'inline-block';
    
    updateGrandTotals();
    alert("Group entries changed and updated successfully!");
}
function enableGroupEditing(button) {
    const row = button.closest('tr');
    const wrapper = button.parentElement;
    
    row.querySelectorAll('.grouped-cell-container input:not([readonly])').forEach(input => {
        input.removeAttribute('disabled');
        input.style.backgroundColor = '#fff'; 
    });

    button.style.display = 'none'; 
    wrapper.querySelector('.btn-save-group').style.display = 'inline-block';
}
</script>

<?= $this->endSection() ?>
