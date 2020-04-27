<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access
?>

<div class="ijs-body">

    <div class="ijs-title-wrapper">
        <h1 class="ijs-title">Instant JS</h1>

        <div class="ijs-theme-options">
            <label for="selectTheme">Color scheme:</label>
            <select name="selectTheme" id="selectTheme">
                <option value="vs">VS Light</option>
                <option value="vs-dark">VS Dark</option>
            </select>
        </div>

        <div class="ijs-theme-options">
            <label for="selectMinify">Minify</label>
            <select name="selectMinify" id="selectMinify">
                <option value="on">On</option>
                <option value="off">Off</option>
            </select>
        </div>
    </div>

    <div class="ijs-wrapper">
        <div class="ijs-editor">
            <div class="ijs-error-block">
                <code class="ijs-error-container"></code>
            </div>
            <div id="monaco-editor"></div>
        </div>
        <div class="ijs-options">
            <button class="save-button button button-primary button-large">Save</button>
        </div>
    </div>

</div>