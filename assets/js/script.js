/**
 * CampusVote - Client Side JavaScript
 * Enhances ballot interactions, input validation, confirmation modals
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Candidate Card Selection Highlighting
    const candidateRadios = document.querySelectorAll('.candidate-radio');
    candidateRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            // Find all candidate labels in this position group
            const groupName = radio.getAttribute('name');
            const groupRadios = document.querySelectorAll(`input[name="${groupName}"]`);
            
            groupRadios.forEach(r => {
                const parentLabel = r.closest('.candidate-label');
                if (parentLabel) {
                    parentLabel.classList.toggle('selected', r.checked);
                }
            });
        });
        
        // Initial state check
        if (radio.checked) {
            const parentLabel = radio.closest('.candidate-label');
            if (parentLabel) parentLabel.classList.add('selected');
        }
    });

    // 2. Ballot Submission Confirmation
    const ballotForm = document.getElementById('ballotForm');
    if (ballotForm) {
        ballotForm.addEventListener('submit', (e) => {
            // Check if all positions have a selection
            const positionCards = document.querySelectorAll('.position-card');
            let allAnswered = true;
            let missingPosition = '';

            positionCards.forEach(card => {
                const radios = card.querySelectorAll('input[type="radio"]');
                if (radios.length > 0) {
                    const checked = card.querySelector('input[type="radio"]:checked');
                    if (!checked) {
                        allAnswered = false;
                        const title = card.querySelector('.position-title h3');
                        if (title && !missingPosition) missingPosition = title.innerText;
                    }
                }
            });

            if (!allAnswered) {
                const proceed = confirm(`Notice: You have not chosen a candidate for: "${missingPosition}".\n\nDo you want to submit anyway? Any blank position will count as an abstained vote.`);
                if (!proceed) {
                    e.preventDefault();
                    return false;
                }
            }

            const confirmSubmit = confirm("ARE YOU SURE?\n\nOnce cast, your ballot is cryptographically sealed and CANNOT be modified or recut.\n\nClick OK to confirm and cast your vote.");
            if (!confirmSubmit) {
                e.preventDefault();
                return false;
            }
        });
    }

    // 3. Auto-fade Flash Alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.6s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 600);
        }, 5000);
    });

    // 4. Admin Search Filter for tables
    const tableSearch = document.getElementById('tableSearch');
    if (tableSearch) {
        tableSearch.addEventListener('keyup', () => {
            const filter = tableSearch.value.toLowerCase();
            const rows = document.querySelectorAll('.custom-table tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});
