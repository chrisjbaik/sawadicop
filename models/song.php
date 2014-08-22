<?php
class Song extends Model {
  public function save() {
    if (empty($this->title)) {
      return false;
    }
    if (empty($this->url)) {
      $this->generateSlug();
    }
    if (!empty($this->chords)) {
      $this->filterLyricsFromChords();
    }

    $this->has_chords = preg_match('/\[[A-G]/', $this->chords);

    //$this->saveFullTextCopy();
    parent::save();
    return true;
  }

  public function keyToString() {
    $keyArray = array(
      0 => 'C',
      1 => 'D♭',
      2 => 'D',
      3 => 'E♭',
      4 => 'E',
      5 => 'F',
      6 => 'F♯',
      7 => 'G',
      8 => 'A♭',
      9 => 'A',
      10 => 'B♭',
      11 => 'B'
    );
    return $keyArray[$this->key];
  }
  
  public function filterLyricsFromChords() {
    $this->lyrics = preg_replace('/\[[^\]]*\]/', '', $this->chords);
    return true;
  }

  public function generateSlug() {
    URLify::$remove_list = array();
    $url = URLify::filter($this->title);
    $found = Model::factory('Song')->where('url', $url)->find_one();

    while ($found) {
      $url = preg_replace_callback('/[-]?([0-9]+)?$/', function ($matches) {
        if (isset($matches[1])) {
          return '-' . ($matches[1] + 1);
        } else if (empty($matches[0])) {
          return '-1';
        } else {
          return;
        }
      }, $url, 1);

      $found = Model::factory('Song')->where('url', $url)->find_one();
    }
    $this->url = $url;

    return true;
  }

  public function tags() {
    return $this->has_many_through('Tag');
  }
}