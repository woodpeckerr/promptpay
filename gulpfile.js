const gulp = require('gulp');
const zip = require('gulp-zip');
const clean = require('gulp-clean');
const fs = require('fs');

const mainFile = 'promptpay.php';
const mainFileContent = fs.readFileSync(mainFile, 'utf8');
const pluginVersion = /^Version.*$/igm.exec(mainFileContent)[0].substring(9).trim();
const pluginName = /^Plugin Name.*$/igm.exec(mainFileContent)[0].substring(13).trim().toLowerCase();
const packageFolderName = pluginName + '-' + pluginVersion;

gulp.task('clean', function () {
  return gulp.src(pluginName + '-*', {read: false})
    .pipe(clean());
});

gulp.task('pack', ['clean'], function () {
  return gulp.src([
    'css/**',
    'image/promptpay.jpg',
    'js/main.min.js',
    'promptpay.php',
    'readme.txt',
    'screenshot-1.jpg',
    'screenshot-2.jpg'
  ], {base: '.'})
    .pipe(gulp.dest(packageFolderName));
});

gulp.task('pack.zip', ['pack'], function () {
  return gulp.src(packageFolderName + '/**')
    .pipe(zip(packageFolderName + '.zip'))
    .pipe(gulp.dest('./'));
});
