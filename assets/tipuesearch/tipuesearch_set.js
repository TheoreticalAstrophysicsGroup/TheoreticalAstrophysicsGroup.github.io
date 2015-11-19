
/*
Tipue Search 5.0
Copyright (c) 2015 Tipue
Tipue Search is released under the MIT License
http://www.tipue.com/search
*/


/*
Stop words
Stop words list from http://www.ranks.nl/stopwords
*/

var tipuesearch_stop_words = ["a", "about", "above", "after", "again", "against", "all", "am", "an", "and", "any", "are", "aren't", "as", "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", "can't", "cannot", "could", "couldn't", "did", "didn't", "do", "does", "doesn't", "doing", "don't", "down", "during", "each", "few", "for", "from", "further", "had", "hadn't", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "he's", "her", "here", "here's", "hers", "herself", "him", "himself", "his", "how", "how's", "i", "i'd", "i'll", "i'm", "i've", "if", "in", "into", "is", "isn't", "it", "it's", "its", "itself", "let's", "me", "more", "most", "mustn't", "my", "myself", "no", "nor", "not", "of", "off", "on", "once", "only", "or", "other", "ought", "our", "ours", "ourselves", "out", "over", "own", "same", "shan't", "she", "she'd", "she'll", "she's", "should", "shouldn't", "so", "some", "such", "than", "that", "that's", "the", "their", "theirs", "them", "themselves", "then", "there", "there's", "these", "they", "they'd", "they'll", "they're", "they've", "this", "those", "through", "to", "too", "under", "until", "up", "very", "was", "wasn't", "we", "we'd", "we'll", "we're", "we've", "were", "weren't", "what", "what's", "when", "when's", "where", "where's", "which", "while", "who", "who's", "whom", "why", "why's", "with", "won't", "would", "wouldn't", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves"];


// Word replace

var tipuesearch_replace = {'words': [
     {'word': 'tipua', 'replace_with': 'tipue'},
     {'word': 'javscript', 'replace_with': 'javascript'},
     {'word': 'jqeury', 'replace_with': 'jquery'}
]};


// Weighting
// Scores are *added* to the internally calculated scores

var baseurl = '/Astro/';
// var baseurl = '/';
var ann_rep_score = -1000;
var ann_pub_score = -1000;

var tipuesearch_weight = {
    'weight': [{
        'url': 'some_url',
            'score': 0
    }, {
        'url': 'some_other_url',
            'score': 0
    }]
};

// Reduce the relevance of annual reports and achievements
for (i = 1998; i < 2050; i++) {
    tipuesearch_weight['weight'].push({ 'url': baseurl + 'achievements/annual_report/ja/' + i.toString() + '/03/31/annual-report/', 'score': ann_rep_score });
    tipuesearch_weight['weight'].push({ 'url': baseurl + 'achievements/publications/ja/' + i.toString() + '/03/31/publications/', 'score': ann_pub_score });
    tipuesearch_weight['weight'].push({ 'url': baseurl + 'achievements/annual_report/en/' + i.toString() + '/03/31/annual-report/', 'score': ann_rep_score });
    tipuesearch_weight['weight'].push({ 'url': baseurl + 'achievements/publications/en/' + i.toString() + '/03/31/publications/', 'score': ann_pub_score });
}


// Stemming
// Stems are just added to search

var tipuesearch_stem = {'words': [
     {'word': 'turbulent', 'stem': 'turbulence'},
     {'word': 'magnetohydrodynamics', 'stem': 'mhd'},
     {'word': 'radiative', 'stem': 'radiation'}
]};


// Internal strings

var tipuesearch_string_1 = 'No title';
var tipuesearch_string_2 = 'Showing results for';
var tipuesearch_string_3 = 'Search instead for';
var tipuesearch_string_4 = '1 result';
var tipuesearch_string_5 = 'results';
var tipuesearch_string_6 = 'Prev';
var tipuesearch_string_7 = 'Next';
var tipuesearch_string_8 = 'Nothing found';
var tipuesearch_string_9 = 'Common words are largely ignored';
var tipuesearch_string_10 = 'Search too short';
var tipuesearch_string_11 = 'Should be one character or more';
var tipuesearch_string_12 = 'Should be';
var tipuesearch_string_13 = 'characters or more';
