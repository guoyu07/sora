<h1>Hello</h1>
<p>This is a demo.</p>

<ul>
  <?php foreach($this->events as $event): ?>
  <li>
    <b><?= link_to($event->name, $event) ?>:</b>
    <p><?= $event->description ?></p>
  </li>
  <?php endforeach; ?>
</ul>
