@props(['rating' => 0, 'editable' => false, 'name' => 'rating'])

<div class="rating-stars {{ $editable ? 'editable' : '' }}" data-rating="{{ $rating }}">
    @for ($i = 1; $i <= 5; $i++)
        @if ($editable)
            <label class="star-label">
                <input type="radio" name="{{ $name }}" value="{{ $i }}" class="star-input" {{ $i == $rating ? 'checked' : '' }}>
                <svg class="star-icon {{ $i <= $rating ? 'filled' : '' }}" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/>
                </svg>
            </label>
        @else
            <svg class="star-icon {{ $i <= $rating ? 'filled' : '' }}" width="20" height="20" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/>
            </svg>
        @endif
    @endfor
</div>

<style>
.rating-stars {
    display: inline-flex;
    gap: 4px;
    align-items: center;
}

.star-icon {
    color: #4b5563;
    transition: color 0.2s ease;
}

.star-icon.filled {
    color: #fbbf24;
}

.rating-stars.editable {
    cursor: pointer;
}

.star-label {
    cursor: pointer;
    display: inline-flex;
    position: relative;
}

.star-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.rating-stars.editable .star-label:hover .star-icon,
.rating-stars.editable .star-label:hover ~ .star-label .star-icon {
    color: #fbbf24;
}

.rating-stars.editable .star-input:checked ~ .star-icon,
.rating-stars.editable .star-input:checked ~ .star-label .star-icon {
    color: #fbbf24;
}

/* Reverse hover effect for proper star highlighting */
.rating-stars.editable:hover .star-icon {
    color: #4b5563;
}

.rating-stars.editable .star-label:hover .star-icon {
    color: #fbbf24;
}

.rating-stars.editable .star-label:has(.star-input:checked) .star-icon,
.rating-stars.editable .star-label:has(.star-input:checked) ~ .star-label .star-icon {
    color: #fbbf24;
}
</style>

@if ($editable)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingContainers = document.querySelectorAll('.rating-stars.editable');

    ratingContainers.forEach(container => {
        const labels = container.querySelectorAll('.star-label');

        labels.forEach((label, index) => {
            label.addEventListener('mouseenter', function() {
                // Highlight all stars up to and including this one
                for (let i = 0; i <= index; i++) {
                    labels[i].querySelector('.star-icon').classList.add('filled');
                }
                // Remove highlight from stars after this one
                for (let i = index + 1; i < labels.length; i++) {
                    labels[i].querySelector('.star-icon').classList.remove('filled');
                }
            });

            label.addEventListener('click', function() {
                const rating = index + 1;
                container.setAttribute('data-rating', rating);

                // Update visual state
                labels.forEach((lbl, i) => {
                    const star = lbl.querySelector('.star-icon');
                    if (i <= index) {
                        star.classList.add('filled');
                    } else {
                        star.classList.remove('filled');
                    }
                });
            });
        });

        container.addEventListener('mouseleave', function() {
            // Restore to selected rating
            const rating = parseInt(container.getAttribute('data-rating') || 0);
            labels.forEach((label, i) => {
                const star = label.querySelector('.star-icon');
                if (i < rating) {
                    star.classList.add('filled');
                } else {
                    star.classList.remove('filled');
                }
            });
        });
    });
});
</script>
@endif
