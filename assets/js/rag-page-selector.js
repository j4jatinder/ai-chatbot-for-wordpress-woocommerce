jQuery(document).ready(function($) {
    const $searchField = $('#rag-page-search');
    const $searchResults = $('#rag-page-search-results');
    const $selectedList = $('#rag-selected-pages-list');
    const $noPagesSelected = $('#rag-no-pages-selected');
    let searchTimeout = null;

    // --- Search Logic (Debounced Input) ---
    $searchField.on('keyup', function() {
        clearTimeout(searchTimeout);
        const searchVal = $(this).val();

        if (searchVal.length < 3) {
            $searchResults.slideUp(100).empty();
            return;
        }

        searchTimeout = setTimeout(function() {
            performPageSearch(searchVal);
        }, 300); // Wait 300ms after typing stops
    });

    function performPageSearch(searchTerm) {
        $searchResults.empty().slideDown(100).append('<li class="loading">' + ragAdmin.i18n.searching + '</li>');

        $.ajax({
            url: ragAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'rag_search_pages',
                s: searchTerm,
                // Nonce protection for security is highly recommended
                '_wpnonce': ragAdmin.nonce 
            },
            success: function(response) {
                $searchResults.empty();
                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(page) {
                        // Check if the page is already selected
                        const isSelected = $selectedList.find('li[data-page-id="' + page.id + '"]').length > 0;
                        if (!isSelected) {
                            $searchResults.append(
                                '<li class="rag-search-result" data-page-id="' + page.id + '" data-page-title="' + page.title + '">' +
                                page.title + 
                                ' <button type="button" class="rag-add-page button button-small">Add</button>' +
                                '</li>'
                            );
                        }
                    });
                    if ($searchResults.is(':empty')) {
                         $searchResults.append('<li>' + ragAdmin.i18n.no_results + '</li>');
                    }
                } else {
                    $searchResults.append('<li>' + ragAdmin.i18n.no_results + '</li>');
                }
            },
            error: function() {
                $searchResults.empty().append('<li>' + ragAdmin.i18n.error + '</li>');
            }
        });
    }

    // --- Add Page Logic ---
    $searchResults.on('click', '.rag-add-page', function() {
        const $li = $(this).closest('li');
        const pageId = $li.data('page-id');
        const pageTitle = $li.data('page-title');

        // 1. Create the new list item for selection
        const selectedHtml = 
            '<li data-page-id="' + pageId + '">' +
                pageTitle + 
                ' <input type="hidden" name="wp_rag_ai_chatbot_policy_pages[]" value="' + pageId + '" />' +
                ' <button type="button" class="rag-remove-page button button-small" data-page-id="' + pageId + '">Remove</button>' +
            '</li>';
        
        $selectedList.append(selectedHtml);

        // 2. Remove the 'No pages selected' placeholder
        $noPagesSelected.remove();

        // 3. Remove from search results and hide if no other results
        $li.remove();
        if ($searchResults.is(':empty')) {
            $searchResults.slideUp(100);
        }

        // 4. Clear search field
        $searchField.val('');
    });

    // --- Remove Page Logic ---
    $selectedList.on('click', '.rag-remove-page', function() {
        $(this).closest('li').remove();

        // If the list is now empty, show the placeholder
        if ($selectedList.is(':empty')) {
            $selectedList.append('<li id="rag-no-pages-selected">' + ragAdmin.i18n.no_pages_selected + '</li>');
        }

        // Re-run search if the search box has text (optional, but helpful)
        if ($searchField.val().length >= 3) {
             performPageSearch($searchField.val());
        }
    });
    
    // Hide results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#rag-policy-pages-selector').length) {
            $searchResults.slideUp(100);
        }
    });
});