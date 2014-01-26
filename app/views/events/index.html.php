<h1>Hello</h1>
<p>This is a demo.</p>

<ul>
  <?php foreach($this->events as $event): ?>
  <li><b><?= $event->name ?>:</b>
      <p><?= $event->description ?></p>
  <?php endforeach; ?>
</ul>