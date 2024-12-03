<?php

use Illuminate\Support\Facades\Route;

// Routes

    Route::get('/', function () {
        // Call the sorting function and destructure the returned array
        list($sort_by, $order) = sort_result(request('sort_by'), request('order'));

        $movies = get_movies($sort_by, $order);
        return view('home')->with('movies', $movies);
        
    })->name('home');
    
    // Show a detailed movie page
    Route::get('/movie/{id}', function ($id) {
        $movie = get_movie_by_id($id);
        $reviews = get_reviews($id);

        $value = Session::get('username', NULL);
        $edit_mode = false;

        if ($value != NULL) {
            $user_id = get_username($value);
            $edit_mode = already_wrote_review($user_id, $id);
        }

        return view('movie-detail', ['movie' => $movie, 'reviews' => $reviews, 'edit_mode' => $edit_mode]);
    })->name('movie');

    // Page to allow user to create a new review
    Route::get('/movie/{id}/review', function($movie_id) {
        $movie = get_movie_by_id($movie_id);
        return view('review', ['movie' => $movie]);
    })->name('review');

    // Page to allow user to edit review
    Route::get('/movie/{id}/edit-review', function($movie_id) {
        $movie = get_movie_by_id($movie_id);
        $value = Session::get('username', NULL);

        if ($value != NULL) {
            $user_id = get_username($value);
            $user_review = get_user_review($user_id, $movie_id);
            return view('edit-review', ['movie' => $movie, 'user_review' => $user_review, 'user_name' => $value]);
        } else {
            die("You cannot access this page.");
        }
    })->name('review.edit');

    // Page to show a list of productions
    Route::get('/productions', function() {
        $productions = get_productions();
        return view('productions', ['productions' => $productions]);
    })->name('productions');

    Route::get("/new-movie", function() {
        return view('new-movie');
    })->name('new-movie');

    // Show a list of movies from the production
    Route::get('production/{name}', function($production_name) {
        // Decode the production name from the URL
        $production_name = urldecode($production_name);

        // Call the sorting function and destructure the returned array
        list($sort_by, $order) = sort_result(request('sort_by'), request('order'));

        $movies = get_movies_by_production($production_name, $sort_by, $order);

        return view('production', [
            'movies' => $movies,
            'production_name' => $production_name
        ]);
    })->name('production');

    // Allow users to submit a new review
    Route::post('submit-review', function(){
        // Get input data from the request
        $username = request('username');
        $movie_id = request('movie_id');
        $rating = request('rating');
        $review_text = request('review_text');

        // Check if all fields are not empty
        if (empty($username) || empty($rating) || empty($review_text)){
            return back()-> with(['errors' => ["Some fields are empty. Please fill out all fields."]]);
        }

        // Check if provided username is valid
        $results = valid_username($username);

        // If result is an array, it means there are validation errors
        if (is_array($results)) {
            return back()->with(['errors' => $results]);
        } else {
            $username = $results;
        }

        Session::put('username', $username);
        $user_id = get_username($username);

        // Check if user already wrote a review
        $already_wrote_review = already_wrote_review($user_id, $movie_id);

        if ($already_wrote_review) {
            return back()->with(['errors' => ["You cannot make several reviews per movie"]]);
        }

        // Check if user created too many reviews on that date
        $is_cap_of_the_day = is_cap_of_the_day($user_id);

        if ($is_cap_of_the_day) {
            return back()->with(['errors' => ["You have reached the daily limit for creating reviews. Please try again tomorrow."]]);
        }

        // Check to identify fake review
        $is_fake_review = is_fake_review($review_text);

        if ($is_fake_review) {
            return back()->with(['errors' => ["Your review text might be a potential fake review and couldn't be submitted. Please modify your review and try again."]]);
        }

        // Add review
        $id = add_review($user_id, $movie_id, $rating, $review_text);

        if ($id) {
            return redirect()->route('movie', ['id' => $movie_id]);
        } else {
            return back()->with(['errors' => ["Something went wrong while adding a review to server"]]);
        };
    })->name('review.create');

    // Allow users to update their own review
    Route::post('update-review', function(){
        // Get input data from the request
        $user_id = request('user_id');
        $movie_id = request('movie_id');
        $rating = request('rating');
        $review_text = request('review_text');

        if (empty($rating) || empty($review_text)){
            return back()->with(['errors' => ["Some fields are empty. Please fill out all fields."]]);
        }

        // Check to identify fake review
        $is_fake_review = is_fake_review($review_text);

        if ($is_fake_review) {
            return back()->with(['errors' => ["Your review text might be a potential fake review and couldn't be submitted. Please modify your review and try again."]]);
        }

        update_review($user_id, $movie_id, $rating, $review_text);

        return redirect()->route('movie', ['id' => $movie_id]);
    })->name('review.update');

    // Allow user to create a new movie
    Route::post('create-movie', function(){
        // Get all input values
        $name = request('name');
        $production = request('production');
        $genre = request('genre');
        $released_date = request('released_date');

        if (empty($name) || empty($production) || empty($genre) || empty($released_date)) {
            return back()->with(['errors' => ["Some fields are empty. Please fill out all fields."]]);
        }

        $valid_movie = valid_name($name);
        $valid_production = valid_name($production);

        if (count($valid_movie) > 0) {
            return back()->with(['errors' => $valid_movie]);
        } else if (count($valid_production) > 0) {
            return back()->with(['errors' => $valid_production]);
        }

        // Handle file upload
        $image_name = null;
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $last_movie = get_last_movie_id();
            $new_id = $last_movie ? $last_movie + 1 : 1;
            $image_name = $new_id . '.' . $image->extension();
            $image->move(public_path('imgs'), $image_name);
        } else {
            return back()->with(['errors' => ["Some fields are empty. Please fill out all fields."]]);
        }

        $id = add_movie($name, $production, $genre, $released_date, $image_name);

        if ($id) {
            return redirect()->route('home');
        } else {
            return back()->with(['errors' => ["Something went wrong while adding a movie to server"]]);
        };
    })->name('movie.create');

    // Allow user to delete a movie
    Route::delete('/movie/{id}/delete', function($movie_id) {
        delete_movie($movie_id);
        return redirect()->route('home');
    })->name('movie.delete');

// Functions

// Sort the result based on user's input
function sort_result($request_sort_by, $request_order) {
    $sort_by = null;
    $order = null;

    // Determine sorting criteria
    if ($request_sort_by == "average_rating") {
        $sort_by = "average_rating";
    } else {
        $sort_by = "review_count";
    }

    // Determine ordering
    if ($request_order == "asc") {
        $order = "ASC";
    } else {
        $order = "DESC";
    }

    // Return the sorting and ordering as an array
    return [$sort_by, $order];
}

// Check if username is valid
function valid_username($username) {

    $errors = [];

    // Validation 1: Username length and symbols
    if (strlen($username) <= 2) {
        $errors[] = 'The username must be more than 2 characters long.';
    }

    if (preg_match('/[-_+]/', $username)) {
        $errors[] = 'The username cannot contain -, _, or + symbols.';
    }

    // Validation 2: Remove odd numbers from the username
    $modified_username = preg_replace('/\d*[13579]\d*/', '', $username);

if ($modified_username !== $username) {
    Session::flash('username_modified', "The username you entered has been changed to: $modified_username.");
    $username = $modified_username;
}

    if ($modified_username !== $username) {
        Session::flash('username_modified', "The username you entered has been changed to: $modified_username.");
        $username = $modified_username;
    }

    if (count($errors) > 0) {
        return $errors;
    } else {
        return $username;
    }
}

// This is to check if movie name and production value are valid
function valid_name($name) {
    $errors = [];

    // Validation 1: Name length and symbols
    if (strlen($name) <= 2) {
        $errors[] = 'The name must be longer than 2 characters.';
    }

    if (preg_match('/[-_+]/', $name)) {
        $errors[] = 'The name cannot contain special characters like "-", "_", or "+".';
    }

    return $errors;
}

// Add a new movie
function add_movie( $name, $production, $genre, $released_date, $image_name) {
    $sql = 
    "INSERT INTO movies (name, production, genre, released_date, image_path)
    VALUES (?, ?, ?, ?, ?)";

    DB::insert($sql, array($name, $production, $genre, $released_date, $image_name));
    $id = DB::getPdo()->lastInsertId();
    
    return $id;
}

// Get movies with sorting
function get_movies($sort_by, $order) {
    // https://www.w3schools.com/sql/sql_join_left.asp
    // https://www.w3schools.com/mysql/func_mysql_ifnull.asp
    $sql = 
    "SELECT movies.id, movies.name, movies.image_path, IFNULL(AVG(reviews.rating), 0) AS average_rating, IFNULL(COUNT(review_text), 0) AS review_count 
    FROM movies
    LEFT JOIN reviews ON reviews.movie_id = movies.id
    GROUP BY movies.id, movies.name, movies.image_path
    ORDER BY $sort_by $order";

    $movies = DB::select($sql);

    return $movies;
}

// Get a movie detail by its id
function get_movie_by_id($id){
    $sql = 
    "SELECT movies.id, movies.name, movies.production, movies.genre, movies.released_date, movies.image_path, 
            IFNULL(AVG(reviews.rating), 0) AS average_rating, IFNULL(COUNT(review_text), 0) AS review_count 
    FROM movies
    LEFT JOIN reviews ON reviews.movie_id = movies.id
    WHERE movies.id = ?
    GROUP BY movies.id, movies.name, movies.production, movies.genre, movies.released_date, movies.image_path";

    $movies = DB::select($sql, array($id));

    // If we get more than one item or no items display an error
    if(count($movies) != 1) {
        die("Invalid query or result: $sql\n");
    }

    // Extract the first item (which should be the only item)
    $movie = $movies[0];
    return $movie;
}

// Get a list of reviews which was written for its specified movie id
function get_reviews($id) {
    $sql = 
    "SELECT reviews.rating, reviews.review_text, reviews.date, users.name
    FROM reviews, users
    WHERE users.id = reviews.user_id
    AND movie_id = ?";

    $reviews = DB::select($sql, array($id));

    return $reviews;
}

// Get a review from a specified user
function get_user_review($user_id, $movie_id) {
    $sql = 
    "SELECT * 
    FROM reviews
    WHERE user_id = ?
    AND movie_id = ?";
    
    // Execute the SQL query
    $reviews = DB::select($sql, array($user_id, $movie_id));

    if(count($reviews) != 1) {
        die("Invalid query or result: $sql\n");
    }

    // Extract the first item (which should be the only item)
    $review = $reviews[0];
    return $review;
}

// Get username
function get_username($username) {
    $sql = 
    "SELECT id
    FROM users
    WHERE name = ?";

    $user_id = DB::select($sql, array($username));

    if(count($user_id) == 0) {
        $insert_sql = 
        "INSERT INTO users (name) VALUES (?)";

        DB::insert($insert_sql, array($username));

        $user_id = DB::getPdo()->lastInsertId();
    } else {
        $user_id = $user_id[0]->id;
    }

    return $user_id;
}

// Add a review
function add_review($user_id, $movie_id, $rating, $review_text) {
    $sql = 
    "INSERT INTO reviews (user_id, movie_id, rating, review_text, date) 
    VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)";

    DB::insert($sql, array($user_id, $movie_id, $rating, $review_text));
    $id = DB::getPdo()->lastInsertId();
    
    return $id;
}

// Updtae a review 
function update_review($user_id, $movie_id, $rating, $review_text) {
    $sql = "
    UPDATE reviews 
    SET rating = ?, review_text = ? 
    WHERE user_id = ?
    AND movie_id = ?";

    DB::update($sql, array($rating, $review_text, $user_id, $movie_id));
}

// To check if current user already wrote an review
function already_wrote_review($user_id, $movie_id) {
    $sql = 
    "SELECT *
    FROM reviews
    WHERE user_id = ?
    AND movie_id = ?";

    $review = DB::select($sql, array($user_id, $movie_id));

    return count($review) > 0;
}

// Get a list of productions
function get_productions() {
    $sql =
    "SELECT movies.production, IFNULL(AVG(reviews.rating), 0) AS average_rating
    FROM movies
    LEFT JOIN reviews ON movies.id = reviews.movie_id
    GROUP BY movies.production
    ORDER BY average_rating DESC";

    $productions = DB::select($sql);

    return $productions;
}

// Get movies produced by a specified production
function get_movies_by_production($production_name, $sort_by, $order){
    $sql = 
    "SELECT movies.id, movies.name, movies.image_path, IFNULL(AVG(reviews.rating), 0) AS average_rating, IFNULL(COUNT(reviews.review_text), 0) AS review_count 
    FROM movies
    LEFT JOIN reviews ON reviews.movie_id = movies.id
    WHERE movies.production = ?
    GROUP BY movies.id, movies.name, movies.image_path
    ORDER BY $sort_by $order";

    $movies = DB::select($sql, array($production_name));

    return $movies;
}


// Get the id of the movie uploaded at most recently
function get_last_movie_id(){
    $sql =
    "SELECT id
    FROM movies
    ORDER BY id DESC
    LIMIT 1";

    $last_movie = DB::select($sql);

    if (!empty($last_movie)) {
        return $last_movie[0]->id;
    }

    return null;
}

// Delete movie 
function delete_movie($movie_id){
    $review_sql = 
    "DELETE FROM reviews 
    WHERE movie_id = ?";

    DB::delete($review_sql, array($movie_id));

    $movie_sql = 
    "DELETE FROM movies 
    WHERE id = ?";

    DB::delete($movie_sql, array($movie_id));
}

// Function to identify fake reviews (User uploaded too mnay reviews in a day)
function is_cap_of_the_day($user_id) {
    $sql = "
    SELECT *
    FROM reviews
    WHERE user_id = ?
    AND DATE(date) = DATE('now')";

    $reviews = DB::select($sql, array($user_id));

    if (count($reviews) > 5) {
        return true;
    }
    return false;
}


// Function to identify fake reviews
function is_fake_review($review_text) {
    // Check for bad words
    // Reference: https://en.wiktionary.org/wiki/Category:English_swear_words
    // https://github.com/LDNOOBW/List-of-Dirty-Naughty-Obscene-and-Otherwise-Bad-Words/blob/master/en
    $bad_words = [
    'arse', 'arsehead', 'arsehole', 'ass', 'ass hole', 'asshole', 'bastard', 'bitch', 'bloody', 
    'bollocks', 'brotherfucker', 'bugger', 'bullshit', 'child-fucker', 'Christ on a bike', 
    'Christ on a cracker', 'cock', 'cocksucker', 'crap', 'cunt', 'dammit', 'damn', 'damned', 
    'damn it', 'dick', 'dick-head', 'dickhead', 'dumb ass', 'dumb-ass', 'dumbass', 'dyke', 
    'father-fucker', 'fatherfucker', 'fuck', 'fucker', 'fucking', 'god dammit', 'god damn', 
    'goddammit', 'God damn', 'goddamn', 'Goddamn', 'goddamned', 'goddamnit', 'godsdamn', 'hell', 
    'holy shit', 'horseshit', 'in shit', 'jack-ass', 'jackarse', 'jackass', 'Jesus Christ', 'Jesus fuck', 
    'Jesus H. Christ', 'Jesus Harold Christ', 'Jesus, Mary and Joseph', 'Jesus wept', 'kike', 
    'mother fucker', 'mother-fucker', 'motherfucker', 'nigga', 'nigra', 'pigfucker', 'piss', 
    'prick', 'pussy', 'shit', 'shit ass', 'shite', 'sibling fucker', 'sisterfuck', 'sisterfucker', 
    'slut', 'son of a bitch', 'son of a whore', 'spastic', 'sweet Jesus', 'twat', 'wanker',
    '2g1c', '2 girls 1 cup', 'acrotomophilia', 'alabama hot pocket', 'alaskan pipeline', 'anal',
    'anilingus', 'anus', 'apeshit', 'auto erotic', 'autoerotic', 'babeland', 'baby batter', 'baby juice', 
    'ball gag', 'ball gravy', 'ball kicking', 'ball licking', 'ball sack', 'ball sucking', 'bangbros',
    'bangbus', 'bareback', 'barely legal', 'barenaked', 'bastardo', 'bastinado', 'bbw', 'bdsm', 'beaner', 
    'beaners', 'beaver cleaver', 'beaver lips', 'beastiality', 'bestiality', 'big black', 'big breasts', 
    'big knockers', 'big tits', 'bimbos', 'birdlock', 'black cock', 'blonde action', 'blonde on blonde action', 
    'blowjob', 'blow job', 'blow your load', 'blue waffle', 'blumpkin', 'bondage', 'boner', 'boob', 'boobs', 
    'booty call', 'brown showers', 'brunette action', 'bukkake', 'bulldyke', 'bung hole', 'bunghole', 'busty',
    'butt', 'buttcheeks', 'butthole', 'camel toe', 'camgirl', 'camslut', 'camwhore', 'carpet muncher', 
    'carpetmuncher', 'chocolate rosebuds', 'cialis', 'circlejerk', 'cleveland steamer', 'clit', 'clitoris', 
    'clover clamps', 'clusterfuck', 'coprolagnia', 'coprophilia', 'cornhole', 'coon', 'coons', 'creampie', 
    'cum', 'cumming', 'cumshot', 'cumshots', 'cunnilingus', 'darkie', 'date rape', 'daterape', 'deep throat', 
    'deepthroat', 'dendrophilia', 'dingleberry', 'dingleberries', 'dirty pillows', 'dirty sanchez', 
    'doggie style', 'doggiestyle', 'doggy style', 'doggystyle', 'dolcett', 'domination', 'dominatrix', 'dommes', 
    'donkey punch', 'double dong', 'double penetration', 'dp action', 'dry hump', 'dvda', 'eat my ass', 'ecchi', 
    'ejaculation', 'erotic', 'erotism', 'escort', 'eunuch', 'fag', 'fecal', 'felch', 'fellatio', 'feltch', 
    'female squirting', 'femdom', 'figging', 'fingerbang', 'fingering', 'fisting', 'foot fetish', 'footjob', 
    'frotting', 'fuck buttons', 'fuckin', 'fucktards', 'fudge packer', 'fudgepacker', 'futanari', 'gangbang', 
    'gang bang', 'gay sex', 'genitals', 'giant cock', 'girl on', 'girl on top', 'girls gone wild', 'goatcx', 
    'goatse', 'gokkun', 'golden shower', 'goo girl', 'goregasm', 'grope', 'group sex', 'g-spot', 'guro', 'hand job', 
    'handjob', 'hard core', 'hardcore', 'hentai', 'homoerotic', 'honkey', 'hooker', 'horny', 'hot carl', 'hot chick', 
    'how to kill', 'how to murder', 'huge fat', 'humping', 'incest', 'intercourse', 'jack off', 'jail bait', 
    'jailbait', 'jelly donut', 'jerk off', 'jigaboo', 'jiggaboo', 'jiggerboo', 'jizz', 'juggs', 'kinbaku', 
    'kinkster', 'kinky', 'knobbing', 'leather restraint', 'leather straight jacket', 'lemon party', 'livesex', 
    'lolita', 'lovemaking', 'make me come', 'male squirting', 'masturbate', 'masturbating', 'masturbation', 
    'menage a trois', 'milf', 'missionary position', 'mong', 'mound of venus', 'mr hands', 'muff diver', 
    'muffdiving', 'nambla', 'nawashi', 'negro', 'neonazi', 'nig nog', 'nimphomania', 'nipple', 'nipples', 'nsfw', 
    'nsfw images', 'nude', 'nudity', 'nympho', 'nymphomania', 'octopussy', 'omorashi', 'one guy one jar', 
    'orgasm', 'orgy', 'paedophile', 'paki', 'panties', 'panty', 'pegging', 'penis', 'phone sex', 'piece of shit', 
    'pikey', 'pissing', 'piss pig', 'pisspig', 'playboy', 'pole smoker', 'ponyplay', 'poon', 'poontang', 'porn', 
    'porno', 'pornography', 'prince albert piercing', 'pthc', 'pubes', 'queaf', 'queef', 'quim', 'raghead', 
    'raging boner', 'rape', 'raping', 'rapist', 'rectum', 'rimjob', 'rimming', 'rosy palm', 'rosy palm and her 5 sisters', 
    'rusty trombone', 'sadism', 'santorum', 'scat', 'schlong', 'scissoring', 'semen', 'sex', 'sexcam', 'sexo', 
    'sexy', 'sexual', 'sexuality', 'shaved beaver', 'shaved pussy', 'shemale', 'shibari', 'shitblimp', 'shitty', 
    'shota', 'shrimping', 'skeet', 'slanteye', 'snowballing', 'sodomize', 'sodomy', 'spunk', 'strap on', 'strapon', 
    'strappado', 'strip club', 'tied up', 'tit', 'tits', 'titties', 'titty', 'tongue in a', 'topless', 'tosser', 
    'towelhead', 'tranny', 'tribadism', 'tub girl', 'tushy', 'undressing', 'upskirt', 'urethra play', 'vagina', 
    'viagra', 'vibrator', 'violet wand', 'voyeur', 'wrinkled starfish', 'xxx', 'yaoi', 'yellow showers', 'zoophilia'
    ];

    foreach ($bad_words as $bad_word) {
        if (stripos($review_text, $bad_word) !== false) {
            return true;
        }
    }


    // Check for phone numbers
    $phone_regex = ('/^[0-9]{10}+$/');
    if (preg_match($phone_regex, $review_text)) {
        return true;
    }


    // Check for email addresses
    $email_regex = '/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}\b/i';
    if (preg_match($email_regex, $review_text)) {
        return true;
    }


    // Check for links/URLs
    $url_regex = '/https?:\/\/[^\s]+/'; // Matches URLs starting with http or https
    if (preg_match($url_regex, $review_text)) {
        return true;
    }


    // Too many positive or negative words in a review
    // https://www.pw.live/exams/school/positive-words/
    $positive_words = [
    'energise', 'amuse', 'ecstatic', 'polite', 'generous', 'attractive', 'assure', 'beaming', 
    'loved', 'care', 'benignant', 'best', 'good', 'better', 'breathtaking', 'comforting', 
    'cheerful', 'optimistic', 'positive', 'dignified', 'reliable', 'disciplined', 'accomplished', 
    'brave', 'affirm', 'accurate', 'classy', 'adept', 'admirable', 'connected', 'clever', 
    'affectionate', 'colourful', 'valiant', 'exciting', 'exultant', 'courageous', 'delectable', 
    'innovative', 'charismatic', 'daring', 'instinctive', 'mighty', 'informative', 'luminous', 
    'natural', 'miraculous', 'affluent', 'nice', 'kind-hearted', 'amazing', 'impressive', 
    'determined', 'ideal', 'excellent', 'honest', 'enchanting', 'magical', 'honourable', 
    'responsible', 'fashionable', 'keen', 'assertive', 'joyous', 'magnificent', 'confident', 
    'friendly', 'patient', 'enthusiastic', 'humane', 'lucky', 'peaceful', 'loyal', 'mindful', 
    'insightful', 'noble', 'glorious', 'intelligent', 'capable', 'hospitable', 'perfect', 
    'persistent', 'empowered', 'lively', 'obedient', 'ravishing', 'philanthropic', 'obliging', 
    'passionate', 'outgoing', 'radiant', 'intellectual', 'likeable', 'neat', 'perceptive', 
    'quality', 'valour', 'polished', 'quick', 'spellbinding', 'sensational', 'goal-oriented', 
    'skillful', 'talented', 'promising', 'relaxing', 'thrilling', 'perseverance', 'relevant', 
    'proactive', 'upbeat', 'serene', 'tactful', 'thoughtful', 'tenacious', 'stellar', 
    'picturesque', 'refined', 'sensible', 'phenomenal', 'visionary', 'uplifting', 'striking', 
    'vibrant', 'upstanding', 'quick-witted', 'sincere', 'smart', 'resilient', 'unique', 
    'wholehearted', 'persuasive', 'timely', 'warm', 'unbelievable', 'impartial', 'tolerant', 
    'well-read', 'unselfish', 'splendiferous', 'whimsical', 'remarkable', 'enticing', 
    'sportive', 'organised', 'bubbly', 'trustworthy', 'precise', 'brainy', 'reasonable', 
    'productive', 'beautiful', 'charismatic', 'professional', 'devout', 'focused', 'alive', 
    'exultant', 'pleasing', 'exhilarating', 'bewitching', 'non-judgemental', 'fearless', 
    'open-minded', 'realistic', 'memorable', 'achievement', 'favourite', 'good-looking', 
    'witty', 'affability', 'bold', 'glittering', 'abundant', 'glee', 'judicious', 'prompt', 
    'joysome', 'outstanding', 'pally', 'methodical', 'refulgent', 'savvy', 'transcendental', 
    'remarkable', 'exciting', 'grateful', 'graceful', 'generous', 'great', 'joyful', 'hopeful', 
    'thrilled', 'terrific', 'amazing', 'spectacular', 'fantastic', 'marvelous', 'awesome', 
    'superb', 'perfect', 'wonderful'
    ];
    // https://gist.github.com/mkulakowski2/4289441
    $negative_words = [
    'abnormal', 'abolish', 'abomination', 'abort', 'abrupt', 'absence', 'absurd', 
    'abuse', 'accuse', 'aggressive', 'aggrieve', 'alarming', 'alienate', 'allegation', 
    'ambush', 'anger', 'anguish', 'annoy', 'anxiety', 'apathetic', 'appall', 'arrogant', 
    'ashamed', 'atrocious', 'attack', 'awful', 'bad', 'banal', 'bankrupt', 'barbaric', 
    'barren', 'bash', 'bastard', 'betray', 'bitter', 'blame', 'bleak', 'blind', 'bloodthirsty', 
    'boring', 'broken', 'brutal', 'bully', 'burn', 'chaos', 'cheap', 'childish', 'clueless', 
    'collapse', 'conflict', 'corrupt', 'coward', 'crazy', 'creepy', 'crime', 'cruel', 
    'cry', 'cursed', 'dangerous', 'dark', 'dead', 'deaf', 'deceit', 'deceive', 'defeat', 
    'defective', 'defensive', 'delusion', 'deny', 'depressed', 'destroy', 'devastated', 
    'disagree', 'disappoint', 'disaster', 'disgust', 'dishonest', 'dislike', 'distress', 
    'divisive', 'doubt', 'drained', 'dull', 'dumb', 'evil', 'fail', 'failure', 'fear', 
    'filthy', 'flawed', 'fool', 'forget', 'frantic', 'fright', 'frustrate', 'gloomy', 
    'grief', 'gross', 'guilty', 'harmful', 'hate', 'haunted', 'heartless', 'helpless', 
    'hopeless', 'horrible', 'hostile', 'hurt', 'ignorant', 'ill', 'immature', 'impossible', 
    'incompetent', 'inferior', 'insane', 'jealous', 'lazy', 'liar', 'loathsome', 'mad', 
    'manipulative', 'mess', 'miserable', 'misfortune', 'monster', 'nasty', 'neglect', 
    'nonsense', 'offend', 'pain', 'paranoid', 'pitiful', 'poor', 'prejudice', 'regret', 
    'reject', 'rude', 'sad', 'scared', 'selfish', 'shame', 'sinister', 'slow', 'sorrow', 
    'stubborn', 'stupid', 'suffering', 'terrible', 'threat', 'toxic', 'tragic', 'ugly', 
    'unbearable', 'unfair', 'unhappy', 'unjust', 'unreliable', 'upset', 'useless', 'vicious', 
    'violent', 'weak', 'worthless', 'worst', 'wound'
    ];

    $positive_count = 0;
    $negative_count = 0;
    
    // Too many positive or negative words in a review
    foreach ($positive_words as $word) {
        if (stripos($review_text, $word) !== false) {
            $positive_count++;
        }
    }

    foreach ($negative_words as $word) {
        if (stripos($review_text, $word) !== false) {
            $negative_count++;
        }
    }

    if ($positive_count > 5 || $negative_count > 5) {
        return true;
    }


    // Too short or long review
    $length = strlen($review_text);
    if ($length < 50) {
        return "This review is too short and may not be genuine.";
    } elseif ($length > 1000) {
        return "This review is too long and could be overly exaggerated.";
    }

    // Check for repeated words
    // The array_count_values() function counts all the values of an array.
    $words = array_count_values(str_word_count(strtolower($review_text), 1));
    foreach ($words as $word => $count) {
        if ($count > 10) {
            return true;
        }
    }

    // If no issues are found
    return false;
}